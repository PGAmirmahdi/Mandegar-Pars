<?php

namespace App\Imports;

use App\Models\Customer;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;

class PublicImport implements ToModel
{
    public function model(array $row)
    {
        // customers
        $phone = trim(str_replace('-','',$row[1]));
        $customer = Customer::wherePhone1($phone)->first();

        if ($customer){
//            $customer->province = $row[2];
//            $customer->city = $row[2];
//            $customer->address1 = $row[2];
            $customer->code = $row[3];
            $customer->save();
        }else{
            return new Customer([
                'user_id' => 4,
                'type' => 'private',
                'customer_type' => 'tehran',
                'economical_number' => 0,
                'national_number' => 0,
                'province' => 'تهران',
                'city' => 'تهران',
                'address1' => 'تهران',
                'postal_code' => 0,
                'code' => $row[3],
                'name' => trim($row[0]),
                'phone1' => trim(str_replace('-','',$row[1])),
            ]);
        }
    }
}
