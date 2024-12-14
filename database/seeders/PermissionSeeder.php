<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
//            'users-list' => 'لیست کاربران',
//            'users-create' => 'ایجاد کاربر',
//            'users-edit' => 'ویرایش کاربر',
//            'users-delete' => 'حذف کاربر',
//
//            'roles-list' => 'لیست نقش ها',
//            'roles-create' => 'ایجاد نقش',
//            'roles-edit' => 'ویرایش نقش',
//            'roles-delete' => 'حذف نقش',
//
//            'categories-list' => 'لیست دسته بندی ها',
//            'categories-create' => 'ایجاد دسته بندی',
//            'categories-edit' => 'ویرایش دسته بندی',
//            'categories-delete' => 'حذف دسته بندی',
//
//            'products-list' => 'لیست محصولات',
//            'products-create' => 'ایجاد محصول',
//            'products-edit' => 'ویرایش محصول',
//            'products-delete' => 'حذف محصول',
//
//            'printers-list' => 'لیست پرینتر ها',
//            'printers-create' => 'ایجاد پرینتر',
//            'printers-edit' => 'ویرایش پرینتر',
//            'printers-delete' => 'حذف پرینتر',
//
//            'invoices-list' => 'لیست سفارشات فروش',
//            'invoices-create' => 'ایجاد سفارش فروش',
//            'invoices-edit' => 'ویرایش سفارش فروش',
//            'invoices-delete' => 'حذف سفارش فروش',
//
//            'system-user' => 'سامانه',
//            'partner-tehran-user' => 'همکار تهران',
//            'partner-other-user' => 'همکار شهرستان',
//            'single-price-user' => 'تک فروشی',
//
//            'coupons-list' => 'لیست کد تخفیف',
//            'coupons-create' => 'ایجاد کد تخفیف',
//            'coupons-edit' => 'ویرایش کد تخفیف',
//            'coupons-delete' => 'حذف کد تخفیف',
//
//            'packets-list' => 'لیست بسته های ارسالی',
//            'packets-create' => 'ایجاد بسته ارسالی',
//            'packets-edit' => 'ویرایش بسته ارسالی ',
//            'packets-delete' => 'حذف بسته ارسالی',

//            'customers-list' => 'لیست مشتریان',
//            'customers-create' => 'ایجاد مشتری',
//            'customers-edit' => 'ویرایش مشتری ',
//            'customers-delete' => 'حذف مشتری',

//            'tasks-list' => 'لیست وظایف',
//            'tasks-create' => 'ایجاد وظیفه',
//            'tasks-edit' => 'ویرایش وظیفه ',
//            'tasks-delete' => 'حذف وظیفه',

//            'notes-list' => 'لیست یادداشت ها',
//            'notes-create' => 'ایجاد یادداشت',
//            'notes-edit' => 'ویرایش یادداشت ',
//            'notes-delete' => 'حذف یادداشت',

//            'leaves-list' => 'لیست مرخصی',
//            'leaves-create' => 'درخواست مرخصی',
//            'leaves-delete' => 'حذف درخواست مرخصی',

//            'ceo' => 'مدیرعامل',
//            'prices-list' => 'لیست قیمت ها',
//            'price-history' => 'تاریخچه قیمت ها',
//            'shops' => 'فروشگاه ها',
//            'accountant' => 'حسابدار',

//            'sale-reports-list' => 'لیست گزارشات فروش',
//            'sale-reports-create' => 'ایجاد گزارش فروش',
//            'sale-reports-edit' => 'ویرایش گزارش فروش ',
//            'sale-reports-delete' => 'حذف گزارش فروش',

        // inventory
//           'warehouse-keeper' => 'انباردار',

//           'warehouses-list' => 'لیست انبارها',
//           'warehouses-create' => 'ایجاد انبار',
//           'warehouses-edit' => 'ویرایش انبار',
//           'warehouses-delete' => 'حذف انبار',

//           'inventory-list' => 'انبار - لیست کالاها',
//           'inventory-create' => 'انبار - ثبت کالا',
//           'inventory-edit' => 'انبار - ویرایش کالا',
//           'inventory-delete' => 'انبار - حذف کالا',
//
//            'input-reports-list' => 'انبار - لیست ورودی ها',
//            'input-reports-create' => 'انبار - ثبت ورودی',
//            'input-reports-edit' => 'انبار - ویرایش ورودی',
//            'input-reports-delete' => 'انبار - حذف ورودی',
//
//            'output-reports-list' => 'انبار - لیست خروجی ها',
//            'output-reports-create' => 'انبار - ثبت خروجی',
//            'output-reports-edit' => 'انبار - ویرایش خروجی',
//            'output-reports-delete' => 'انبار - حذف خروجی',
        // end inventory

//            'foreign-customers-list' => 'لیست مشتریان خارجی',
//            'foreign-customers-create' => 'ثبت مشتری خارجی',
//            'foreign-customers-edit' => 'ویرایش مشتری خارجی',
//            'foreign-customers-delete' => 'حذف مشتری خارجی',

//            'tickets-list' => 'لیست تیکت ها',
//            'tickets-create' => 'ثبت تیکت',
//            'tickets-delete' => 'حذف تیکت',

//            'sms-histories' => 'پیام های ارسال شده',
//            'exit-door' => 'درب خروج'

//            'bot-manager' => 'مدیریت ربات تلگرام'

//            'reports-list' => 'لیست گزارشات روزانه',
//            'reports-create' => 'ثبت گزارش',
//            'reports-edit' => 'ویرایش گزارش',
//            'reports-delete' => 'حذف گزارش',

//            'artin-products-list' => 'لیست محصولات آرتین',
//            'artin-products-edit' => 'ویرایش محصول آرتین',

//            'software-updates-list' => 'لیست تغییرات نرم افزار',
//            'software-updates-create' => 'ثبت تغییرات نرم افزار',
//            'software-updates-edit' => 'ویرایش تغییرات نرم افزار',
//            'software-updates-delete' => 'حذف تغییرات نرم افزار',

//            'guarantees-list' => 'لیست گارانتی ها',
//            'guarantees-create' => 'ثبت گارانتی',
//            'guarantees-edit' => 'ویرایش گارانتی',
//            'guarantees-delete' => 'حذف گارانتی',

//            'sales-manager' => 'مدیر فروش'
//            'unofficial-sales' => 'فروش غیر رسمی'

//            'price-requests-list' => 'لیست درخواست قیمت',
//            'price-requests-create' => 'ثبت درخواست قیمت',
//            'price-requests-delete' => 'حذف درخواست قیمت',

//            'buy-orders-list' => 'سفارشات خرید',
//            'buy-orders-create' => 'ثبت سفارش خرید',
//            'buy-orders-edit' => 'ویرایش سفارش خرید',
//            'buy-orders-delete' => 'حذف سفارش خرید',

            'delivery-day' => 'انتخاب روزهای تحویل سفارش',
        ];

        foreach ($items as $key => $item)
        {
            $permission = Permission::create([
                'name' => $key,
                'label' => $item,
            ]);

            $role = Role::whereName('admin')->first();
            $role->permissions()->attach($permission->id);

//            $roles = Role::all();
//            foreach ($roles as $role){
//                $role->permissions()->attach($permission->id);
//            }
        }
    }
}
