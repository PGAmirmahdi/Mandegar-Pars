<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeaveInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (User::all() as $user){
            $exist = DB::table('leave_info')->where('user_id', $user->id)->first();
            if (!$exist){
                DB::table('leave_info')->insert([
                    'user_id' => $user->id,
                    'count' => 2,
                    'month_updated' => verta()->month,
                ]);
            }
        }
    }
}
