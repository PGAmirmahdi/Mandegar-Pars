<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\BotUser;
use App\Models\Factor;
use App\Models\Guarantee;
use App\Models\Inventory;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Printer;
use App\Models\Product;
use App\Models\User;
use App\Notifications\SendMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    public function createInvoice(Request $request)
    {
        Log::info('Request data: ', $request->all());
        try {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'phone' => 'required|string|digits_between:10,15',
                'national_number' => 'required|string',
                'province' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'address_1' => 'required|string|max:500',
                'postal_code' => 'required|string',
                'created_in' => 'required|string|in:website,application',
                'payment_type' => 'required|string|in:cash,credit',
                'items' => 'required|array|min:1',
                'items.*.acc_code' => 'required|string|exists:products,code',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.total' => 'required|numeric|min:0',
            ]);
            if ($validator->fails()) {
                // اگر اعتبارسنجی شکست خورد، خطاها را لاگ کرده و در پاسخ JSON برمی‌گردانیم
                Log::error('Validation errors: ', $validator->errors()->toArray());
                return response()->json([
                    'error' => 'Validation failed',
                    'details' => $validator->errors()
                ], 422);
            }
            $data = $request->all();
            // محاسبه هزینه ارسال
            $shipping_cost = $request->input('shipping_cost', 0);
            $role_id = \App\Models\Role::whereHas('permissions', function ($permission) {
                $permission->where('name', 'single-price-user');
            })->pluck('id');
            $single_price_user = User::whereIn('role_id', $role_id)->latest()->first();

            $notifiables = User::whereHas('role', function ($role) {
                $role->whereHas('permissions', function ($q) {
                    $q->whereIn('name', ['single-price-user', 'sales-manager']);
                });
            })->get();
            $tax = 0.1;
            if ($data['created_in'] == 'website') {
                $notif_message = 'یک سفارش از سایت آرتین دریافت گردید';
                $notif_title= 'سفارش از سایت';
            } else {
                $notif_message = 'یک سفارش از اپلیکیشن آرتین دریافت گردید';
                $notif_title= 'سفارش از اپلیکیشن';
            }

            $url = route('invoices.index');
            Notification::send($notifiables, new SendMessage($notif_title,$notif_message, $url));

            $customer = \App\Models\Customer::updateOrCreate(
                ['phone1' => $data['phone']],
                [
                    'user_id' => $single_price_user->id,
                    'name' => $data['first_name'] . ' ' . $data['last_name'],
                    'type' => 'private',
                    'economical_number' => 0,
                    'province' => $data['province'],
                    'city' => $data['city'],
                    'national_number' => $data['national_number'],
                    'address1' => $data['address_1'],
                    'postal_code' => $data['postal_code'],
                    'phone1' => $data['phone'],
                    'customer_type' => 'single-sale',
                    'code' => 'CU-' . random_int(1000000, 9999999),
                ]
            );
            // ایجاد سفارش و ذخیره محصولات به صورت JSON
            $products = [];
            foreach ($request->items as $item2) {
                $products = array_map(function ($item2) {
                    $product = Product::where('code', $item2['acc_code'])->first();
                    return [
                        'products' => (string)$product->id, // تغییر به id
                        'colors' => 'black',
                        'counts' => (string)$item2['quantity'], // تبدیل به رشته
                        'units' => 'number',
                        'prices' => (string)($item2['total'] / $item2['quantity']) * 10 * 110/100, // قیمت واحد
                        'total_prices' => (string)$item2['total'] * 10 * 110/100, // قیمت کل
                    ];
                }, $data['items']);
            }
            $order = \App\Models\Order::create([
                'description' => 'خرید از سایت',
                'type' => 'private',
                'req_for' => 'invoice',
                'payment_type' => 'cash',
                'code' => $this->generateCode(),
                'user_id' => $single_price_user->id,
                'customer_id' => $customer->id,
                'created_in' => $data['created_in'] ,
                'shipping_cost' => $shipping_cost * 10,
                'products' => json_encode($products), // ذخیره محصولات به صورت JSON
            ]);
            $order->order_status()->updateOrCreate(
                ['status' => 'register'],
                ['orders' => 1, 'status' => 'register']
            );
            $invoice = \App\Models\Invoice::create([
                'user_id' => $single_price_user->id,
                'customer_id' => $customer->id,
                'order_id' => $order->id,
                'economical_number' => 0,
                'payment_type' => $data['payment_type'],
                'province' => $customer->province,
                'city' => $customer->city,
                'national_number' => $customer->national_number,
                'address' => $customer->address1,
                'postal_code' => $customer->postal_code,
                'phone' => $customer->phone1,
                'status' => 'invoiced',
                'created_in' => $data['created_in'],
                'discount' => 0,
                'description' => $data['customer_note'],
                'shipping_cost' => $shipping_cost * 10,
            ]);
            $data3 = [
                'user_id' => 173,
                'action' => 'ایجاد سفارش فروش',
                'description' => 'کاربر اسدی بیگزاد محله(ادمین) ' . 'برای مشتری به نام ' . $customer->name  . "یک سفارش فروش به شماره " . $invoice->id . ' ایجاد کرد',
            ];

            Activity::create($data3);
            foreach ($request->items as $item) {
                $product = Product::where('code', $item['acc_code'])->first();

                $price = ($item['total'] / $item['quantity']) * 10; // تبدیل قیمت واحد به ریال
                $total = $item['total'] * 10; // تبدیل قیمت کل به ریال

                $invoice->products()->attach($product->id, [
                    'color' => 'black',
                    'count' => $item['quantity'],
                    'price' => $price,
                    'total_price' => $total,
                    'discount_amount' => 0,
                    'tax' => $total * $tax,
                    'invoice_net' => (int)$total + ($total * $tax), // هزینه ارسال را هم در محاسبه نهایی در نظر بگیرید
                ]);
                $invoice->factor()->updateOrCreate(['status' => 'paid']);
            }
            Log::info('Order processed successfully', ['data' => $data]);
            return response()->json([
                'success' => true,
                'message' => 'سفارش با موفقیت از طریق سایت در اتوماسیون ایجاد شد',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            // ثبت خطا در لاگ
            Log::error('Error processing order data: ' . $e->getMessage(), [
                'exception' => $e,
            ]);
            // اگر بخواهید می‌توانید یک پاسخ خطا به کاربر بازگردانید
            return response()->json(['error' => 'Unable to process order.'], 500);
        }
    }

    public function getInvoiceProducts(Request $request)
    {
        $invoice = Invoice::find($request->invoice_id);

        $invoice_other_products = $invoice->other_products;
        $invoice_products_code = $invoice->products->pluck('code')->toArray();
        $products_code = Product::pluck('code')->toArray();
        $missed = false;
        $miss_products = [];

        if (array_intersect($invoice_products_code, $products_code) != $invoice_products_code) {
            $missed = true;
            $miss_products = array_diff($invoice_products_code, $products_code);
            $miss_products = implode(', ', $miss_products);
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
        if ($brand) {
            return Printer::whereBrand($brand)->pluck('name', 'id');
        }

        return Printer::pluck('name', 'id');
    }

    public function getCartridges($printer_id)
    {
        $cartridges = Printer::whereId($printer_id)->first()->cartridges;
        $cartridges = explode(',', $cartridges);

        return $cartridges;
    }

    public function createBotUser(Request $request)
    {
        if (!BotUser::where('user_id', $request->id)->first()) {
            BotUser::create([
                'user_id' => $request->id,
                'first_name' => $request->first_name,
                'username' => $request->username,
            ]);
        }
    }
    public function generateCode()
    {
        $code = '666' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);

        while (Order::where('code', $code)->lockForUpdate()->exists()) {
            $code = '666' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
        }

        return $code;
    }

    public function checkGuarantee(Request $request)
    {
        $serial = $request->serial;

        $guarantee = Guarantee::where('serial', $serial)->first();

        if ($guarantee){
            if ($guarantee->status == 'active'){
                $error = false;
                $date = verta($guarantee->expired_at)->format('Y/m/d');
                $message = "گارانتی محصولات شما تا تاریخ $date معتبر است";
            }elseif($guarantee->status == 'inactive'){
                $error = true;
                $message = 'سریال گارانتی معتبر نیست';
            }elseif($guarantee->status == 'voided'){
                $error = true;
                $message = 'گارانتی محصولات شما باطل شده است';
            } else{
                $error = true;
                $message = 'گارانتی محصولات شما منقضی شده است';
            }
        }else{
            $error = true;
            $message = 'سریال گارانتی معتبر نیست';
        }

        $data = [
            'error' => $error,
            'message' => $message,
        ];

        return response()->json(['data' => $data])    ->header('Access-Control-Allow-Origin', 'https://artintoner.com');
    }
}
