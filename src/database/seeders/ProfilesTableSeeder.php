<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfilesTableSeeder extends Seeder
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
            'user_id' => '1',
            'username' => '太郎',
            'address' => '東京都千代田区千代田1-1',
            'building' => '花園町',
            'image' => 'watch.jpg',
            'postal_code' => '100-8111'
        ];
        DB::table('profiles')->insert($param);
    }
}
