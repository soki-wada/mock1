<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $param = [
            'name' => '山田一郎',
            'email' => 'yamada@gmail.com',
            'password' => Hash::make('12345678')
        ];
        DB::table('users')->insert($param);
        $param = [
            'name' => '佐藤次郎',
            'email' => 'sato@gmail.com',
            'password' => Hash::make('87654321')
        ];
        DB::table('users')->insert($param);
        $param = [
            'name' => '鈴木三郎',
            'email' => 'suzuki@gmail.com',
            'password' => Hash::make('11111111')
        ];
        DB::table('users')->insert($param);
    }
}
