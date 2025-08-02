<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Product;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;


class IndexTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('conditions')->truncate();
        DB::table('products')->truncate();
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->seed('UsersTableSeeder');
        $this->seed('ConditionsTableSeeder');
        $this->seed('ProductsTableSeeder');
    }

    public function testShowProducts()
    {
        $products = Product::all();

        $response = $this->get('/');
        $response->assertStatus(200);

        foreach($products as $product){
            $response->assertSee($product->name);
        }
    }

    public function testSoldProducts(){
        $product = Product::find(1);
        $product->update(['is_purchased' => 1]);

        $response = $this->get('/');
        $response->assertStatus(200);

        $response->assertSeeInOrder([
            $product->name,
            'sold'
        ]);
    }

    public function testExceptMyProducts(){
        $user = User::factory()->create();

        Product::create([
            'user_id' => $user->id,
            'condition_id' => '1',
            'name' => 'ゲーミングチェア',
            'price' => '15000',
            'description' => 'スタイリッシュなデザインのメンズ腕時計',
            'image' => 'watch.jpg',
            'brand' => 'SEIKO',
            'is_purchased' => '0',
        ]);

        $response = $this->get('/');

        $response->assertSee('ゲーミングチェア');

        $this->actingAs($user);
        $response = $this->get('/');

        $response->assertDontSee('ゲーミングチェア');
    }
}
