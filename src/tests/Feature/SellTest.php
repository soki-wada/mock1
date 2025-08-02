<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Product;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SellTest extends TestCase
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
        DB::table('categories')->truncate();
        DB::table('category_product')->truncate();
        DB::table('conditions')->truncate();
        DB::table('products')->truncate();
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->seed('UsersTableSeeder');
        $this->seed('CategoriesTableSeeder');
        $this->seed('ConditionsTableSeeder');
        $this->seed('ProductsTableSeeder');
        $this->seed('CategoryProductTableSeeder');
        $this->user = User::factory()->create();
    }

    public function testSell()
    {
        Storage::fake('public');
        $this->actingAs($this->user);
        $response = $this->get('/sell');

        $response->assertStatus(200);

        $file = UploadedFile::fake()->image('image.png');

        $response =  $this->post('sell', [
            'user_id' => $this->user->id,
            'is_purchased' => 0,
            'image' => $file,
            'categories' => [1, 2],
            'condition_id' => 1,
            'name' => '机',
            'brand' => 'tt',
            'description' => '説明',
            'price' => 1000
        ]);

        $response->assertSessionHasNoErrors();

        $product = Product::where('user_id', $this->user->id)
            ->where('name', '机')
            ->first();

        $this->assertDatabaseHas('products', [
            'user_id' => $this->user->id,
            'condition_id' => 1,
            'name' => '机',
            'price' => 1000,
            'description' => '説明',
            'brand' => 'tt',
            'is_purchased' => 0,
        ]);

        $this->assertStringEndsWith('_image.png', $product->image);

        $this->assertDatabaseHas('category_product', [
            'product_id' => $product->id,
            'category_id' => 1
        ]);

        $this->assertDatabaseHas('category_product', [
            'product_id' => $product->id,
            'category_id' => 2
        ]);
    }
}
