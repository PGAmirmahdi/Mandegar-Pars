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
        $url = 'https://console.melipayamak.com/api/send/shared/9ac659ce20e74c2288f0b58cb9c4e710';
        $data = array('bodyId' => $bodyId, 'to' => $to, 'args' => $args);
        $data_string = json_encode($data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

        // Next line makes the request absolute insecure
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

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
