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
//            'invoices-list' => 'لیست پیش فاکتور ها',
//            'invoices-create' => 'ایجاد پیش فاکتور',
//            'invoices-edit' => 'ویرایش پیش فاکتور',
//            'invoices-delete' => 'حذف پیش فاکتور',
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
        ];

        foreach ($items as $key => $item)
        {
            $permission = Permission::create([
                'name' => $key,
                'label' => $item,
            ]);

            $role = Role::whereName('admin')->first();
            $role->permissions()->attach($permission->id);
        }
    }
}
