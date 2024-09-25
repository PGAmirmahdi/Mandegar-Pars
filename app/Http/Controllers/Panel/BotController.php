<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BotController extends Controller
{
    private $token = '6972066054:AAGJS9VIFQ9_oC0lrsJzpdhxWHgVU9rdHMk';

    public function profile()
    {
        $this->authorize('bot-manager');

        $this->getMyName();

        $name = $this->getMyName()['result']['name'];
        $description = $this->getMyDescription()['result']['description'];
        $shortDescription = $this->getMyShortDescription()['result']['short_description'];

        return view('panel.bot.profile', compact('name' ,'description','shortDescription'));
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
        $url = $this->getUrl().'/getMyName';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $result = curl_exec($ch);
        curl_close($ch);

        dd(curl_error($ch));
        return json_decode($result, true);
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
