<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\BotUser;
use App\Models\Factor;
use App\Models\Inventory;
use App\Models\Invoice;
use App\Models\Printer;
use App\Models\Product;
use App\Models\User;
use App\Notifications\SendMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class ApiController extends Controller
{
    public function createInvoice(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|digits_between:10,15',
            'national_code' => 'required|string|size:10',
            'province' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address_1' => 'required|string|max:500',
            'postal_code' => 'required|string|digits:6',
            'created_in' => 'required|string|in:website,application',
            'payment_type' => 'required|string|in:cash,credit',
            'items' => 'required|array|min:1',
            'items.*.acc_code' => 'required|string|exists:products,code',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.total' => 'required|numeric|min:0',
        ], [
            'first_name.required' => 'فیلد نام الزامی است.',
            'last_name.required' => 'فیلد نام خانوادگی الزامی است.',
            'phone.required' => 'شماره تلفن الزامی است.',
            'national_code.required' => 'کد ملی الزامی است.',
            'province.required' => 'فیلد استان الزامی است.',
            'city.required' => 'فیلد شهر الزامی است.',
            'address_1.required' => 'آدرس الزامی است.',
            'postal_code.required' => 'کد پستی الزامی است.',
            'created_in.required' => 'منبع ایجاد سفارش الزامی است.',
            'payment_type.required' => 'نوع پرداخت الزامی است.',
            'items.required' => 'حداقل یک آیتم باید انتخاب شود.',
            'items.*.acc_code.required' => 'کد محصول الزامی است.',
            'items.*.quantity.required' => 'تعداد آیتم باید وارد شود.',
            'items.*.total.required' => 'قیمت کل باید وارد شود.',
        ]);
        $data = $request->all();

        $role_id = \App\Models\Role::whereHas('permissions', function ($permission){
            $permission->where('name', 'single-price-user');
        })->pluck('id');
        $single_price_user = User::whereIn('role_id', $role_id)->latest()->first();

        $notifiables = User::whereHas('role' , function ($role) {
            $role->whereHas('permissions', function ($q) {
                $q->whereIn('name', ['single-price-user','sales-manager']);
            });
        })->get();

        if ($request->input('created_in') == 'website'){
            $notif_message = 'یک سفارش از سایت آرتین دریافت گردید';
        } else {
            $notif_message = 'یک سفارش از اپلیکیشن آرتین دریافت گردید';
        }

        $url = route('invoices.index');
        Notification::send($notifiables, new SendMessage($notif_message, $url));

        $customer = \App\Models\Customer::where('phone1', $data['phone'])->firstOrCreate([
            'user_id' => $single_price_user->id,
            'name' => $request->input('first_name').' '.$request->input('last_name'),
            'type' => 'private',
            'economical_number' => 0,
            'province' => $request->input('province'),
            'city' => $request->input('city'),
            'address1' => $request->input('address_1'),
            'postal_code' => $request->input('postal_code'),
            'phone1' => $request->input('phone'),
            'customer_type' => 'single-sale',
        ]);

        $invoice = \App\Models\Invoice::create([
            'user_id' => $single_price_user->id,
            'customer_id' => $customer->id,
            'economical_number' => 0,
            'province' => $customer->province,
            'city' => $customer->city,
            'address' => $customer->address1,
            'postal_code' => $customer->postal_code,
            'phone' => $customer->phone1,
            'status' => 'order',
            'created_in' => $request->input('created_in'),
            'discount' => 0,
        ]);

        $tax = 0.1;

        foreach ($request->items as $item){
            $product = Product::where('code', $item['acc_code'])->first();
            $price = ($item['total'] / $item['quantity']);
            $total = $item['total'];

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
        $invoice = Invoice::find($request->invoice_id);

        $invoice_other_products = $invoice->other_products;
        $invoice_products_code = $invoice->products->pluck('code')->toArray();
        $inventory_products_code = Inventory::pluck('code')->toArray();
        $missed = false;
        $miss_products = [];

        if (array_intersect($invoice_products_code, $inventory_products_code) != $invoice_products_code) {
            $missed = true;
            $miss_products = array_diff($invoice_products_code, $inventory_products_code);
            $miss_products = implode(', ',$miss_products);
        }

        return response()->json([
            'data' => $invoice->products,
            'missed' => $missed,
            'miss_products' => $miss_products,
            'other_products' => $invoice_other_products,
            'invoice_id' => $invoice->id
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
        $cartridges = Printer::whereId($printer_id)->first()->cartridges;
        $cartridges = explode(',',$cartridges);

        return $cartridges;
    }

    public function createBotUser(Request $request)
    {
        if (!BotUser::where('user_id', $request->id)->first()){
            BotUser::create([
                'user_id' => $request->id,
                'first_name' => $request->first_name,
                'username' => $request->username,
            ]);
        }
    }
}
