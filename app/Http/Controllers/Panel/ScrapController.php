<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ScrapController extends Controller
{
    public function index($website)
    {
        switch ($website)
        {
            case 'torob':
                return $this->torob();
            default:
                return '';
        }
    }

    private function torob()
    {
        $url = "https://torob.com/p/72d546da-59e6-41f4-8906-ab04d96fd1d9/کارتریج-پرینتر-اچ-پی-مدل-p2035/";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        $response = curl_exec($ch);
        curl_close($ch);

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->validateOnParse = true;
        $dom->loadHTML($response);
        libxml_clear_errors();

        $res = json_decode($dom->getElementsByTagName('script')->item(0)->nodeValue);
        $data = $res->offers->offers;

        return view('panel.scrap.torob', compact('data'));
    }
}
