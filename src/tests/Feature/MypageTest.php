<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Product;
use App\Models\Profile;
use App\Models\Purchase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Faker\Factory as FakerFactory;
use Faker\Generator;


class MypageTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;
    protected User $user;
    protected Profile $profile;
    protected Product $product;
    protected Purchase $purchase;
    protected Generator $faker;

    protected function setUp(): void
    {
        parent::setUp();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('conditions')->truncate();
        DB::table('products')->truncate();
        DB::table('users')->truncate();
        DB::table('purchases')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->seed('UsersTableSeeder');
        $this->seed('ConditionsTableSeeder');
        $this->seed('ProductsTableSeeder');
        $this->user = User::factory()->create();
        $this->faker = FakerFactory::create('ja_JP');
        $this->profile = Profile::create([
            'user_id' => $this->user->id,
            'username' => $this->faker->name(),
            'address' => $this->faker->address,
            'building' => $this->faker->secondaryAddress,
            'image' => 'test.jpg',
            'postal_code' => $this->faker->postcode
        ]);
        $this->product=
        Product::create([
            'user_id' => $this->user->id,
            'condition_id' => 1,
            'name' => '机',
            'price' => '1000',
            'description' => '説明',
            'image' => 'table.png',
            'brand' => 'tt',
            'is_purchased' => 0
        ]);
        $this->purchase=
        Purchase::create([
            'user_id' => $this->user->id,
            'product_id' => 1,
            'payment' => 1,
            'address' => $this->profile->address,
            'building' => $this->profile->building,
            'postal_code' => $this->profile->postal_code
        ]);
        $this->assertNotEquals(
            Product::find(1)->user_id,
            $this->user->id,
            'product_id=1 の出品者がログインユーザーと同じです。Seederの内容を確認してください。'
        );
    }


    public function testMypage()
    {
        $this->actingAs($this->user);
        $response = $this->get('/mypage?tab=sell');
        $response->assertStatus(200);

        $response->assertSee($this->profile->image);
        $response->assertSee($this->profile->username);
        $response->assertSee($this->product->name);

        $response = $this->get('/mypage?tab=buy');
        $response->assertStatus(200);

        $response->assertSee($this->purchase->product->name);
    }
}
