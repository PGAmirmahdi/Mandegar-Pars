<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BotController extends Controller
{
    private $token;
    public function __construct()
    {
        $this->token = env('TELEGRAM_BOT_TOKEN');
    }

    public function profile()
    {
        $this->authorize('Telegram-bot');

        $nameData = $this->getMyName();
        $descriptionData = $this->getMyDescription();
        $shortDescriptionData = $this->getMyShortDescription();

        // بررسی اینکه نتیجه‌گیری‌ها `null` نیستند
        if (is_null($nameData) || !isset($nameData['result']['name'])) {
            return "Unable to retrieve bot name from Telegram API.";
        }

        if (is_null($descriptionData) || !isset($descriptionData['result']['description'])) {
            return "Unable to retrieve bot description from Telegram API.";
        }

        if (is_null($shortDescriptionData) || !isset($shortDescriptionData['result']['short_description'])) {
            return "Unable to retrieve bot short description from Telegram API.";
        }

        $name = $nameData['result']['name'];
        $description = $descriptionData['result']['description'];
        $shortDescription = $shortDescriptionData['result']['short_description'];

        return view('panel.bot.profile', compact('name', 'description', 'shortDescription'));
    }


    public function editProfile(Request $request)
    {
        $this->setMyName($request->name);
        $this->setMyDescription($request->description);
        $this->setMyShortDescription($request->short_description);

        alert()->success('مشخصات ربات با موفقیت بروزرسانی شد','ویرایش مشخصات ربات');
        return back();
    }

    private function getMyName()
    {
        $url = $this->getUrl().'/getMe';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            dd('Curl error: ' . curl_error($ch));
        }

        curl_close($ch);

        $response = json_decode($result, true);
        if (isset($response['ok']) && $response['ok'] === true) {
            return $response;
        } else {
            dd('Unable to retrieve bot name from Telegram API. Response:', $response);
        }
    }

    private function getMyDescription()
    {
        $url = $this->getUrl().'/getMyDescription';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true);
    }

    private function getMyShortDescription()
    {
        $url = $this->getUrl().'/getMyShortDescription';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true);
    }

    private function setMyName($name)
    {
        $url = $this->getUrl().'/setMyName?name='.$name;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true);
    }

    private function setMyDescription($description)
    {
        $url = $this->getUrl().'/setMyDescription?description='.$description;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true);
    }

    private function setMyShortDescription($short_description)
    {
        $url = $this->getUrl().'/setMyShortDescription?short_description='.$short_description;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true);
    }

    private function getUrl()
    {
        return 'https://api.telegram.org/bot'.$this->token;
    }
}
