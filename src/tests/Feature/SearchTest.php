<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Product;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class SearchTest extends TestCase
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
        DB::table('favorites')->insert([
            'user_id' => $this->user->id,
            'product_id' => 2,
        ]);
    }

    public function testSearch()
    {
        $response = $this->get('/search?keyword=時計&tab=false');

        $response->assertStatus(200);

        $products = Product::all();
        foreach ($products as $product) {
            if ($product->name === '腕時計') {
                $response->assertSee('腕時計');
            } else {
                $response->assertDontSee($product->name);
            }
        }
    }

    public function testSearchMylist(){
        $response = $this->get('/search?keyword=時計&tab=false');
        $this->actingAs($this->user);

        $response->assertStatus(200);

        $products = Product::all();
        foreach ($products as $product) {
            if ($product->name === '腕時計') {
                $response->assertSee('腕時計');
            } else {
                $response->assertDontSee($product->name);
            }
        }

        $response->assertSee('/search?tab=mylist&keyword=時計');
    }
}
