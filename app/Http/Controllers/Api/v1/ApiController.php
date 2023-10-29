<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function createInvoice(Request $request)
    {
        $data = $request->all();

        // create customer
        $customer = \App\Models\Customer::where('phone1', $data['phone'])->firstOrCreate([
            'name' => $data['first_name'].' '.$data['last_name'],
            'type' => 'private',
            'economical_number' => 0,
            'national_number' => $data['national_number'],
            'province' => $data['province'],
            'city' => $data['city'],
            'address1' => $data['address_1'],
            'postal_code' => $data['postal_code'],
            'phone1' => $data['phone'],
        ]);

        // users where has single-price-user permission
        $role_id = \App\Models\Role::whereHas('permissions', function ($permission){
            $permission->where('name', 'single-price-user');
        })->pluck('id');
        $single_price_user = User::where('role_id', $role_id)->first();
        // end users where has single-price-user permission

        // create invoice
        \App\Models\Invoice::create([
            'user_id' => $single_price_user->id,
            'customer_id' => $customer->id,
            'economical_number' => 0,
            'national_number' => $customer->national_number,
            'province' => $customer->province,
            'city' => $customer->city,
            'address' => $customer->address1,
            'postal_code' => $customer->postal_code,
            'phone' => $customer->phone1,
            'status' => $data['status'],
        ]);
    }
}
