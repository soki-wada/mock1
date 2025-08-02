<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Product;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;


class MylistTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('conditions')->truncate();
        DB::table('products')->truncate();
        DB::table('users')->truncate();
        DB::table('favorites')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->seed('UsersTableSeeder');
        $this->seed('ConditionsTableSeeder');
        $this->seed('ProductsTableSeeder');
        $this->user = User::factory()->create();
        DB::table('favorites')->insert([
            'user_id' => $this->user->id,
            'product_id' => 1,
        ]);
    }

    public function testShowMylist()
    {
        $products = Product::all();
        $this->actingAs($this->user);
        $response = $this->get('/?tab=mylist');
        $response->assertStatus(200);
        foreach($products as $product){
            if($product->id == 1){
                $response->assertSee($product->name);
            }else{
                $response->assertDontSee($product->name);
            }
        }
    }

    public function testSoldMylist(){
        $product = Product::find(1);
        $this->actingAs($this->user);
        $product->update(['is_purchased' => 1]);
        $response = $this->get('/?tab=mylist');
        $response->assertStatus(200);

        $response->assertSeeInOrder([
            $product->name,
            'sold'
        ]);
    }

    public function testExceptMylist(){
        Product::create([
            'user_id' => $this->user->id,
            'condition_id' => '1',
            'name' => 'ゲーミングチェア',
            'price' => '15000',
            'description' => 'スタイリッシュなデザインのメンズ腕時計',
            'image' => 'watch.jpg',
            'brand' => 'SEIKO',
            'is_purchased' => '0',
        ]);

        $this->actingAs($this->user);
        $response = $this->get('/?tab=mylist');

        $response->assertDontSee('ゲーミングチェア');
    }

    public function testGuestMylist(){
        $response = $this->get('/?tab=mylist');
        $products = Product::all();

        foreach($products as $product){
            $response->assertDontSee($product->name);
        }
    }
}
