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
    function sendSMS(int $bodyId, string $to, array $args)
    {
        // مقادیر ثابت نام کاربری و رمز عبور
        $username = '09336533433';
        $password = '31$9#';

        // تبدیل آرگومان‌ها به یک رشته جدا شده با ;
        $text = implode(';', $args);

        // تنظیم داده‌ها برای ارسال پیامک
        $data = array(
            'username' => $username,
            'password' => $password,
            'text' => $text,
            'to' => $to,
            'bodyId' => $bodyId
        );

        $post_data = http_build_query($data);

        // آغاز عملیات cURL برای ارسال درخواست
        $handle = curl_init('https://rest.payamak-panel.com/api/SendSMS/BaseServiceNumber');
        curl_setopt($handle, CURLOPT_HTTPHEADER, array(
            'content-type' => 'application/x-www-form-urlencoded'
        ));
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $post_data);

        $response = curl_exec($handle);
        curl_close($handle);

        return json_decode($response, true); // نتیجه به صورت آرایه برگردانده می‌شود
    }
}
