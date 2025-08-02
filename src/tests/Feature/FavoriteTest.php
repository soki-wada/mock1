<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Profile;
use App\Models\Product;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Faker\Factory as FakerFactory;
use Faker\Generator;


class FavoriteTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    protected User $user;
    protected Profile $profile;
    protected Generator $faker;

    protected function setUp(): void
    {
        parent::setUp();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('categories')->truncate();
        DB::table('category_product')->truncate();
        DB::table('conditions')->truncate();
        DB::table('products')->truncate();
        DB::table('users')->truncate();
        DB::table('favorites')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->seed('UsersTableSeeder');
        $this->seed('CategoriesTableSeeder');
        $this->seed('ConditionsTableSeeder');
        $this->seed('ProductsTableSeeder');
        $this->seed('CategoryProductTableSeeder');
        $this->faker = FakerFactory::create('ja_JP');
        $this->user = User::factory()->create();
        $this->profile = Profile::create([
            'user_id' => $this->user->id,
            'username' => $this->faker->name(),
            'address' => $this->faker->address,
            'building' => $this->faker->secondaryAddress,
            'image' => 'test.jpg',
            'postal_code' => $this->faker->postcode
        ]);
    }

    public function testFavorite()
    {
        $product = Product::find(1);

        $this->actingAs($this->user);

        $response = $this->get('/item/' . $product->id);
        $this->assertMatchesRegularExpression('/<p class="product-icon-amount favorite-count">\s*0\s*<\/p>/', $response->getContent());

        $response = $this->postJson('/favorite/toggle', [
            'item_id' => $product->id,
        ]);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $this->user->id,
            'product_id' => $product->id,
        ]);

        $response = $this->get('/item/' . $product->id);
        $this->assertMatchesRegularExpression('/<p class="product-icon-amount favorite-count">\s*1\s*<\/p>/', $response->getContent());
    }

    public function testCheckedFavorite(){
        $product = Product::find(1);

        $this->actingAs($this->user);

        $response = $this->get('/item/' . $product->id);

        $this->assertStringNotContainsString('<input type="checkbox" id="favorite-toggle" class="favorite-checkbox" checked>', $response->getContent());
        $this->postJson('/favorite/toggle', ['item_id' => $product->id]);
        $this->assertDatabaseHas('favorites', [
            'user_id' => $this->user->id,
            'product_id' => $product->id,
        ]);

        $response = $this->get('/item/' . $product->id);
        $this->assertStringContainsString('<input type="checkbox" id="favorite-toggle" class="favorite-checkbox" checked>', $response->getContent());
    }

    public function testDeleteFavorite(){
        $product = Product::find(1);

        $this->actingAs($this->user);

        $response = $this->get('/item/' . $product->id);

        $this->postJson('/favorite/toggle', ['item_id' => $product->id]);
        $this->assertDatabaseHas('favorites', [
            'user_id' => $this->user->id,
            'product_id' => $product->id,
        ]);

        $response = $this->get('/item/' . $product->id);
        $this->assertMatchesRegularExpression('/<p class="product-icon-amount favorite-count">\s*1\s*<\/p>/', $response->getContent());

        $this->postJson('/favorite/toggle', ['item_id' => $product->id]);
        $this->assertDatabaseMissing('favorites', [
            'user_id' => $this->user->id,
            'product_id' => $product->id,
        ]);

        $response = $this->get('/item/' . $product->id);
        $this->assertMatchesRegularExpression('/<p class="product-icon-amount favorite-count">\s*0\s*<\/p>/', $response->getContent());
    }
}
