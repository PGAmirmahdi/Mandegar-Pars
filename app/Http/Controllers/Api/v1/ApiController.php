<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Factor;
use App\Models\Inventory;
use App\Models\Printer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    public function createInvoice(Request $request)
    {
        $data = $request->all();
//
//        Log::info($data);

        // users where has single-price-user permission
        $role_id = \App\Models\Role::whereHas('permissions', function ($permission){
            $permission->where('name', 'single-price-user');
        })->pluck('id');
        $single_price_user = User::whereIn('role_id', $role_id)->first();
        // end users where has single-price-user permission

        // create customer
        $customer = \App\Models\Customer::where('phone1', $data['phone'])->firstOrCreate([
            'user_id' => $single_price_user->id,
            'name' => $data['first_name'].' '.$data['last_name'],
            'type' => 'private',
            'economical_number' => 0,
            'national_number' => $data['national_code'],
            'province' => $data['province'],
            'city' => $data['city'],
            'address1' => $data['address_1'],
            'postal_code' => $data['postal_code'],
            'phone1' => $data['phone'],
            'customer_type' => 'single-sale',
        ]);


        // create invoice
        $invoice = \App\Models\Invoice::create([
            'user_id' => $single_price_user->id,
            'customer_id' => $customer->id,
            'economical_number' => 0,
            'national_number' => $customer->national_number,
            'province' => $customer->province,
            'city' => $customer->city,
            'address' => $customer->address1,
            'postal_code' => $customer->postal_code,
            'phone' => $customer->phone1,
            'status' => 'invoiced',
            'created_in' => 'website',
        ]);

        $tax = 0.09;

        // create product items
        foreach ($request->items as $item){
            // for test
//            $product = Product::first();
            // end for test

            $product = Product::where('code', $item['acc_code'])->first();

            $price = ($item['total'] / $item['quantity']) .'0';
            $total = $item['total'].'0';

            $invoice->products()->attach($product->id, [
                'color' => 'black',
                'count' => $item['quantity'],
                'price' => $price,
                'total_price' => $total,
                'discount_amount' => 0,
                'extra_amount' => 0,
                'tax' => $total * $tax,
                'invoice_net' => (int)$total + ($total * $tax),
            ]);

            $invoice->factor()->updateOrCreate(['status' => 'paid']);
        }
    }

    public function getInvoiceProducts(Request $request)
    {
        $factor = Factor::find($request->factor_id);

        $invoice_other_products = $factor->invoice->other_products;
        $invoice_products_code = $factor->invoice->products->pluck('code')->toArray();
        $inventory_products_code = Inventory::pluck('code')->toArray();
        $missed = false;
        $miss_products = [];

        if (array_intersect($invoice_products_code, $inventory_products_code) != $invoice_products_code) {
            $missed = true;
            $miss_products = array_diff($invoice_products_code, $inventory_products_code);
            $miss_products = implode(', ',$miss_products);
        }

        return response()->json([
            'data' => $factor->invoice->products,
            'missed' => $missed,
            'miss_products' => $miss_products,
            'other_products' => $invoice_other_products,
            'invoice_id' => $factor->invoice_id
        ]);
    }

    public function getPrinterBrands()
    {
        return Printer::BRANDS;
    }

    public function getPrinters(string $brand = null)
    {
        if ($brand){
            return Printer::whereBrand($brand)->pluck('name','id');
        }

        return Printer::pluck('name','id');
    }

    public function getCartridges($printer_id)
    {
        $cartridges = Printer::whereId($printer_id)->pluck('cartridges');
        $cartridges = explode(',',$cartridges);
        return $cartridges;
    }
}
