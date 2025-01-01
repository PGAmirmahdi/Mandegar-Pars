<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

if (!function_exists('active_sidebar')){
    function active_sidebar(array $items){
        $route = Route::current()->uri;
        $data = [];

        foreach ($items as $value) {
            if ($value == 'panel')
            {
                $data[] = "panel";
            } else{
                $data[] = "panel/".$value;
            }
        }
        if (in_array($route, $data)) {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('make_slug')){
    function make_slug(string $string)
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
        return $slug;
    }
}

if (!function_exists('upload_file')) {
    function upload_file($file, $folder)
    {
        if ($file) {
            $filename = time() . $file->getClientOriginalName();
            $year = Carbon::now()->year;
            $month = Carbon::now()->month;
            $path = public_path("/uploads/{$folder}/{$year}/{$month}/");
            $file->move($path, $filename);
            $img = "/uploads/{$folder}/{$year}/{$month}/" . $filename;
            return $img;
        }
    }
}
if (!function_exists('calculateTotal')){
    function calculateTotal($order)
    {
        $products = json_decode($order->products);
        $sum_total_price = 0;
        if (!empty($products->products)) {

            foreach ($products->products as $product) {
                $sum_total_price += $product->total_prices;
            }
        }

        if (!empty($products->other_products)) {
            foreach ($products->other_products as $product) {
                $sum_total_price += $product->other_total_prices;
            }
        }
        return $sum_total_price;
    }
}
if (!function_exists('calculateTotalInvoice')){
    function calculateTotalInvoice($products)
    {
//        dd($products);
        $sum_total = 0;



        foreach ($products as $product) {

            $sum_total += $product->invoice_net;
        }

        return $sum_total;
    }
}
if (!function_exists('upload_file_factor')) {
    function upload_file_factor($file, $folder)
    {
        if ($file) {
            try {
                $pdfFile = $file;
                $paperFormat = getPaperSizeFromPdf($file);
                $inputPdfPath = $pdfFile->getPathName();

                $outputPdfTempPath = storage_path('app/public_html/temp-processed-pdf.pdf');



                $imagePath = public_path('assets/media/image/stamp.png');


                $mpdf = new \Mpdf\Mpdf([
                    'tempDir' => storage_path('app/mpdf-temp'),
                    'format' => $paperFormat,
                ]);

                $pageCount = $mpdf->SetSourceFile($inputPdfPath);

                list($imgWidth, $imgHeight) = getimagesize($imagePath);
                $imgWidthMm = $imgWidth * 0.164583;
                $imgHeightMm = $imgHeight * 0.164583;

                if ($paperFormat == 'A4'){
                    $x = 280 - $imgWidthMm;
                    $y = 180 - $imgHeightMm;
                }else{
                    $x = 350 - $imgWidthMm;
                    $y = 220 - $imgHeightMm;
                }


                for ($i = 1; $i <= $pageCount; $i++) {
                    $templateId = $mpdf->ImportPage($i);
                    $mpdf->AddPage('L');
                    $mpdf->UseTemplate($templateId);

                    if ($i == $pageCount) {
                        $mpdf->Image($imagePath, $x, $y, $imgWidthMm, $imgHeightMm);
                    }
                }

                $mpdf->Output($outputPdfTempPath, 'F');
                $year = Carbon::now()->year;
                $month = Carbon::now()->month;
                $uploadPath = public_path("/uploads/{$folder}/{$year}/{$month}/");



                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }

                $filename = time() . '-processed.pdf';
                $finalPath = $uploadPath . $filename;
                rename($outputPdfTempPath, $finalPath);


                $img = "/uploads/{$folder}/{$year}/{$month}/" . $filename;
                return $img;
            } catch (Exception $e) {
                alert()->warning('خطا در آپلود فایل', 'خطا');
                return redirect()->to(route('invoices.index'));
            }


        }
    }
}
if (!function_exists('formatBytes')) {
    function formatBytes($size, $precision = 2)
    {
        $base = log($size, 1024);
        $suffixes = array('', 'K', 'M', 'G', 'T');

        return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
    }
}

if (!function_exists('sendSMS')) {
    function sendSMS(int $bodyId, string $to, array $args, array $options = [])
    {
        $url = 'https://console.melipayamak.com/api/send/shared/de3a0dfcc49d4e408041a386f63b4d6f';
        $data = array('bodyId' => $bodyId, 'to' => $to, 'args' => $args);
        $data_string = json_encode($data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

        // Next line makes the request absolute insecure
         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array('Content-Type: application/json',
                'Content-Length: ' . strlen($data_string))
        );
        $result = json_decode(curl_exec($ch));
        curl_close($ch);

        \App\Models\SmsHistory::create([
            'user_id' => auth()->id(),
            'phone' => $to,
            'text' => $options['text'] ?? '',
            'status' => isset($result->recId) ? $result->recId != 11 ? 'sent' : 'failed' : 'failed',
        ]);
        $result = curl_exec($ch);
        if ($result === false) {
            dd('CURL Error: ' . curl_error($ch));
        }
        $result = json_decode($result);
        return $result;

// --------------------------------------------------- //
//        try{
//            $sms = Melipayamak\Laravel\Facade::sms();
//            $from = '50004000425053';
//            $response = $sms->send($to,$from,$text);
//            $json = json_decode($response);
//
//            \App\Models\SmsHistory::create([
//                'user_id' => auth()->id(),
//                'phone' => $to,
//                'text' => $text,
//                'status' => $json->Value != 11 ? 'sent' : 'failed',
//            ]);
//
//            return $json->Value; //RecId or Error Number
//        }catch(Exception $e){
//            return $e->getMessage();
//        }
// --------------------------------------------------- //
    }
}
function getPaperSizeFromPdf($pdfFile)
{
    $inputPdfPath = $pdfFile->getPathName();

    $mpdf = new \Mpdf\Mpdf([
        'tempDir' => storage_path('app/mpdf-temp'),
    ]);

    $pageCount = $mpdf->SetSourceFile($inputPdfPath);

    $page = $mpdf->ImportPage(1);

    $pageSize = $mpdf->getTemplateSize($page);


    $width = round($pageSize['width']);
    $height = round($pageSize['height']);

    $A3Width = 420;
    $A3Height = 297;
    $A4Width =  297;
    $A4Height = 210;

    if ($width >= $A3Width || $height >= $A3Height) {
        return 'A3';
    } else {
        return 'A4';
    }
}

