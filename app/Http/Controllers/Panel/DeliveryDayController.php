<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\DeliveryDay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DeliveryDayController extends Controller
{
    public function index()
    {
        $this->authorize('delivery-day');

        if (\request()->week){
            // days of week
            $items = [
                'saturday' => verta()->addWeek()->startWeek(),
                'sunday' => verta()->addWeek()->startWeek()->addDays(1),
                'monday' => verta()->addWeek()->startWeek()->addDays(2),
                'tuesday' => verta()->addWeek()->startWeek()->addDays(3),
                'wednesday' => verta()->addWeek()->startWeek()->addDays(4),
                'thursday' => verta()->addWeek()->startWeek()->addDays(5),
                'friday' => verta()->addWeek()->startWeek()->addDays(6),
            ];
        }else{
            // days of week
            $items = [
                'saturday' => verta()->startWeek(),
                'sunday' => verta()->startWeek()->addDays(1),
                'monday' => verta()->startWeek()->addDays(2),
                'tuesday' => verta()->startWeek()->addDays(3),
                'wednesday' => verta()->startWeek()->addDays(4),
                'thursday' => verta()->startWeek()->addDays(5),
                'friday' => verta()->startWeek()->addDays(6),
            ];
        }


        $days = $this->getDays($items);

        return view('panel.delivery-days.index', compact('days'));
    }

    private function getDays(array $week)
    {
        $this->authorize('delivery-day');

        $days = [];

        foreach ($week as $day => $item) {
//            $url = "https://holidayapi.ir/jalali/{$item->year}/{$item->month}/{$item->day}";
//
//            $ch = curl_init();
//            curl_setopt($ch, CURLOPT_URL, $url);
//            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//            $res = json_decode(curl_exec($ch));
//            curl_close($ch);

            $is_selected = $this->isSelected($item->format('Y/m/d'));

            $days[$day] = [
                'date' => $item->format('Y/m/d'),
                'text' => $item->formatWord('l'),
//                'is_holiday' => $res->is_holiday,
                'is_holiday' => false,
                'is_selected' => $is_selected,
            ];
        }

        return $days;
    }

    private function isSelected(string $date)
{
    $this->authorize('delivery-day');

    $url = "https://app.mpsystem.ir/api/v1/delivery-day/is-selected?date=$date";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $res = json_decode($response);

    if (json_last_error() !== JSON_ERROR_NONE) {
        // Handle JSON decode error
        // Log the error or return a default value
        return false;
    }

    if (is_null($res) || !isset($res->data)) {
        // Handle the case where the response is null or the data property is missing
        // Log the error or return a default value
        return false;
    }

    return $res->data;
}

}
