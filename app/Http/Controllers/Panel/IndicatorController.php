<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreIndicatorRequest;
use App\Models\Indicator;
use App\Models\User;
use App\Notifications\SendMessage;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Maatwebsite\Excel\Facades\Excel;
use PDF as PDF;

class IndicatorController extends Controller
{
    public function index()
    {
        $indicators = Indicator::query();

        if (auth()->user()->isCEO() || auth()->user()->isAdmin()) {
            if (request()->input('number')) {
                $indicators->where('number', 'LIKE', '%' . request()->input('number') . '%');
            }
        } else {
            $indicators->where('user_id', auth()->id());
            if (request()->input('number')) {
                $indicators->where('number', 'LIKE', '%' . request()->input('number') . '%');
            }
        }

        $indicators = $indicators->latest()->paginate(30);
        return view('panel.indicator.index', compact('indicators'));
    }


    public function create()
    {
        $users = User::where('id', '!=', auth()->id())->get();
        return view('panel.indicator.create', compact(['users']));
    }

    public function store(StoreIndicatorRequest $request)
    {
        $Indicator = new Indicator();
        $Indicator->title = $request->title;
        $Indicator->date = $request->date;
        $Indicator->number = $this->generateUniqueCode();
        $Indicator->attachment = $request->attachment;
        $Indicator->to = $request->to;
        $Indicator->header = $request->header;
        $Indicator->text = $request->text;
        $Indicator->user_id = auth()->id();
        $Indicator->save();

        if (!is_null($request->receiver)) {
            $Indicator->users()->sync($request->receiver);
            $users = User::whereIn('id', $request->receiver)->get();
            $message = 'یک نامه با عنوان ' . $Indicator->title . ' برای شما ارسال شده است';
            Notification::send($users, new SendMessage($message, url('/panel/indicator/inbox')));
        }

        activity_log('create-indicator', __METHOD__, [$request->all(), $Indicator]);
        alert()->success('نامه مورد نظر با موفقیت ثبت شد', 'ثبت نامه');
        return redirect()->route('indicator.index');
    }


    public function show($id)
    {
        //
    }


    public function edit(Indicator $indicator)
    {
        $this->authorize('edit-indicator', $indicator);
        $receivers = $indicator->users->pluck('id')->toArray();
        $users = User::where('id', '!=', auth()->id())->get();
        return view('panel.indicator.edit', compact(['indicator', 'receivers', 'users']));

    }


    public function update(StoreIndicatorRequest $request, $id)
    {
        $indicator = new Indicator();
        $this->authorize('edit-indicator', $indicator);
        $indicator->title = $request->title;
        $indicator->date = $request->date;
        $indicator->number = $this->generateUniqueCode();
        $indicator->attachment = $request->attachment;
        $indicator->header = $request->header;
        $indicator->to = $request->to;
        $indicator->text = $request->text;
        $indicator->save();
        $indicator->users()->sync($request->receiver);
        if (!is_null($request->receiver)) {
            $indicator->users()->sync($request->receiver);
            $users = User::whereIn('id', $request->receiver)->get();
            $message = 'یک نامه با عنوان ' . $indicator->title . ' برای شما ارسال شده است';
            Notification::send($users, new SendMessage($message, url('/panel/indicator/inbox')));
        }
        activity_log('edit-indicator', __METHOD__, [$request->all(), $indicator]);
        alert()->success('نامه مورد نظر با موفقیت ویرایش شد', 'ویرایش نامه');
        return redirect()->route('indicator.index');
    }


//    public function destroy(Indicator $indicator)
//    {
//        activity_log('delete-indicator', __METHOD__, $indicator);
//        $indicator->delete();
//        return back();
//    }


    //export section
//    public function exportToPdf(StoreIndicatorRequest $request)
//    {
//        $title = $request->title;
//        $text = $request->text;
//        $date = $request->date ?? '';
//        $number = $request->number ?? '';
//        $header = $request->header ?? '';
//        $attachment = $request->attachment ?? '';
//        if ($header == 'info') {
//            return $this->exportPdfInfoPersian($title, $text, $date, $number, $attachment);
//        } elseif ($header == 'sale') {
//            return $this->exportPdfSalePersian($title, $text, $date, $number, $attachment);
//        }
//        return $this->exportPdfEnglish($title, $text, $date, $number, $attachment);
//    }

    public function exportExcelIndicator()
    {
        return Excel::download(new \App\Exports\IndicatorsExport, 'indicator.xlsx');
    }


    public function downloadFromIndicator($id)
    {
        $indicator = Indicator::whereId($id)->withTrashed()->first();
//        dd($indicator);
        if ($indicator->header == 'info') {
            return $this->exportPdfInfoPersian($indicator->title, $indicator->text, $indicator->date, $indicator->number, $indicator->attachment);
        } elseif ($indicator->header == 'sale') {
            return $this->exportPdfSalePersian($indicator->title, $indicator->text, $indicator->date, $indicator->number, $indicator->attachment);
        }
        return $this->exportPdfEnglish($indicator->title, $indicator->text, $indicator->date, $indicator->number, $indicator->attachment);
    }


    public function inbox()
    {
        $inbox = auth()->user()->indicators()->withTrashed()->latest()->paginate(30);
        return view('panel.indicator.inbox', compact(['inbox']));
    }


    public function exportPdfInfoPersian($title, $text, $date, $number, $attachment)
    {

        $backgroundImage = public_path('/assets/images/persian-header-info.png');

        $pdf = PDF::loadView('panel.indicator.indicator-header-info-persian-pdf', ['text' => $text, 'date' => $date, 'number' => $number, 'attachment' => $attachment], [], [
            'format' => 'A4',
            'orientation' => 'P',
            'default_font_size' => '10',
            'default_font' => 'sahel',
            'display_mode' => 'fullpage',
            'watermark_text_alpha' => 1,
            'watermark_image_path' => $backgroundImage,
            'watermark_image_alpha' => 1,
            'watermark_image_size' => [210, 297],
            'show_watermark_image' => true,
            'watermarkImgBehind' => true,
        ]);
        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $title . '.pdf"');
    }


    public function exportPdfSalePersian($title, $text, $date, $number, $attachment)
    {
        $backgroundImage = public_path('/assets/images/persian-header-sale.png');
        $pdf = PDF::loadView('panel.indicator.indicator-header-sale-persian-pdf', ['text' => $text, 'date' => $date, 'number' => $number, 'attachment' => $attachment], [], [
            'format' => 'A4',
            'orientation' => 'P',
            'default_font_size' => '10',
            'default_font' => 'sahel',
            'display_mode' => 'fullpage',
            'watermark_text_alpha' => 1,
            'watermark_image_path' => $backgroundImage,
            'watermark_image_alpha' => 1,
            'watermark_image_size' => [210, 297],
            'show_watermark_image' => true,
            'watermarkImgBehind' => true,
        ]);
        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $title . '.pdf"');
    }

    public function exportPdfEnglish($title, $text, $date, $number, $attachment)
    {
        $backgroundImage = public_path('/assets/images/english-header.png');
        $pdf = PDF::loadView('panel.indicator.indicator-header-english-pdf', ['text' => $text, 'date' => $date, 'number' => $number, 'attachment' => $attachment], [], [
            'format' => 'A4',
            'orientation' => 'P',
            'default_font_size' => '10',
            'default_font' => 'sahel',
            'display_mode' => 'fullpage',
            'watermark_text_alpha' => 1,
            'watermark_image_path' => $backgroundImage,
            'watermark_image_alpha' => 1,
            'watermark_image_size' => [210, 297],
            'show_watermark_image' => true,
            'watermarkImgBehind' => true,
        ]);
        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $title . '.pdf"');
    }


//    public function extractName($text)
//    {
//        $tempDiv = new \DOMDocument();
    /*        $tempDiv->loadHTML('<?xml encoding="utf-8" ?>' . $text);*/
//        $spanElements = $tempDiv->getElementsByTagName('span');
//        $fontFamily = null;
//        foreach ($spanElements as $span) {
//            $style = $span->getAttribute('style');
//            preg_match('/font-family\s*:\s*([^;]+)(;|$)/', $style, $matches);
//
//            if (isset($matches[1])) {
//                $fontFamily = trim($matches[1], " '\"");
//                break;
//            }
//        }
//        return $fontFamily ?? 'dana';
//    }

    public function generateUniqueCode()
    {
        // Get current Persian date
        $v = Verta::now();
        $year = $v->format('%y');
        $month = str_pad($v->month, 2, '0', STR_PAD_LEFT);
        $day = str_pad($v->day, 2, '0', STR_PAD_LEFT);

        $dateString = $year . $month . $day;

        do {
            $lastIndicator = Indicator::where('number', 'like', $dateString . '%')->orderBy('number', 'desc')->first();
            if ($lastIndicator) {
                $lastCounter = (int)substr($lastIndicator->number, -3);
                $counter = $lastCounter + 1;
            } else {
                $counter = 1;
            }
            $uniqueCode = $dateString . str_pad($counter, 3, '0', STR_PAD_LEFT);
            $existingIndicator = Indicator::where('number', $uniqueCode)->first();
        } while ($existingIndicator);

        return $uniqueCode;
    }

    public function previewIndicator(Request $request)
    {
//        dd($request->input('header'));
        if ($request->input('header') == 'info'){
            return view('panel.indicator.preview-info', [
                'date' => $request->input('date'),
                'number' => $request->input('number'),
                'attachment' => $request->input('attachment'),
                'text' => $request->input('text')
            ]);
        }
        if ($request->input('header') == 'sale'){
            return view('panel.indicator.preview-sale', [
                'date' => $request->input('date'),
                'number' => $request->input('number'),
                'attachment' => $request->input('attachment'),
                'text' => $request->input('text')
            ]);
        }
        if ($request->input('header') == 'english'){
            return view('panel.indicator.preview-english', [
                'date' => $request->input('date'),
                'number' => $request->input('number'),
                'attachment' => $request->input('attachment'),
                'text' => $request->input('text')
            ]);
        }

    }
}
