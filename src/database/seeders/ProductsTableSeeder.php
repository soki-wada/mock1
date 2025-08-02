<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsTableSeeder extends Seeder
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
            'condition_id' => '1',
            'name' => '腕時計',
            'price' => '15000',
            'description' => 'スタイリッシュなデザインのメンズ腕時計',
            'image' => 'watch.jpg',
            'brand' => 'SEIKO',
            'is_purchased' => '0',
        ];
        DB::table('products')->insert($param);

        $param = [
            'user_id' => '1',
            'condition_id' => '2',
            'name' => 'HDD',
            'price' => '5000',
            'description' => '高速で信頼性の高いハードディスク',
            'image' => 'HDD.jpg',
            'brand' => '',
            'is_purchased' => '0',
        ];
        DB::table('products')->insert($param);

        $param = [
            'user_id' => '1',
            'condition_id' => '3',
            'name' => '玉ねぎ3束',
            'price' => '300',
            'description' => '新鮮な玉ねぎ3束のセット',
            'image' => 'onions.jpg',
            'brand' => '',
            'is_purchased' => '0',
        ];
        DB::table('products')->insert($param);

        $param = [
            'user_id' => '1',
            'condition_id' => '4',
            'name' => '革靴',
            'price' => '4000',
            'description' => 'クラシックなデザインの革靴',
            'image' => 'leather_shoes.jpg',
            'brand' => '',
            'is_purchased' => '0',
        ];
        DB::table('products')->insert($param);

        $param = [
            'user_id' => '1',
            'condition_id' => '1',
            'name' => 'ノートPC',
            'price' => '45000',
            'description' => '高性能なノートパソコン',
            'image' => 'laptop.jpg',
            'brand' => '',
            'is_purchased' => '0',
        ];
        DB::table('products')->insert($param);

        $param = [
            'user_id' => '1',
            'condition_id' => '2',
            'name' => 'マイク',
            'price' => '8000',
            'description' => '高音質のレコーディング用マイク',
            'image' => 'microphone.jpg',
            'brand' => '',
            'is_purchased' => '0',
        ];
        DB::table('products')->insert($param);

        $param = [
            'user_id' => '1',
            'condition_id' => '3',
            'name' => 'ショルダーバッグ',
            'price' => '3500',
            'description' => 'おしゃれなショルダーバッグ',
            'image' => 'bag.jpg',
            'brand' => '',
            'is_purchased' => '0',
        ];
        DB::table('products')->insert($param);

        $param = [
            'user_id' => '1',
            'condition_id' => '4',
            'name' => 'タンブラー',
            'price' => '500',
            'description' => '使いやすいタンブラー',
            'image' => 'tumbler.jpg',
            'brand' => '',
            'is_purchased' => '0',
        ];
        DB::table('products')->insert($param);

        $param = [
            'user_id' => '1',
            'condition_id' => '1',
            'name' => 'コーヒーミル',
            'price' => '4000',
            'description' => '手動のコーヒーミル',
            'image' => 'coffee_maker.jpg',
            'brand' => '',
            'is_purchased' => '0',
        ];
        DB::table('products')->insert($param);

        $param = [
            'user_id' => '1',
            'condition_id' => '2',
            'name' => 'メイクセット',
            'price' => '2500',
            'description' => '便利なメイクアップセット',
            'image' => 'makeup.jpg',
            'brand' => '',
            'is_purchased' => '0',
        ];
        DB::table('products')->insert($param);
    }
}
