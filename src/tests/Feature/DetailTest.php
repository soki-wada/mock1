<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Profile;
use App\Models\Comment;
use App\Models\Favorite;
use App\Models\Product;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Faker\Factory as FakerFactory;
use Faker\Generator;

class DetailTest extends TestCase
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

    public function testShowDetail()
    {
        $products = Product::all();

        foreach($products as $product){
            $categories = $product->categories;
            $comment = Comment::create([
                'user_id' => $this->user->id,
                'product_id'=> $product->id,
                'content' => $this->faker->sentence()
            ]);

            Favorite::create([
                'user_id' => $this->user->id,
                'product_id' => $product->id
            ]);

            $response = $this->get('/item/'. $product->id);

            $response->assertSee('storage/images/' . $product->image);
            $response->assertSee($product->name);
            $response->assertSee(number_format($product->price));
            $this->assertMatchesRegularExpression(
                '/<p class="product-icon-amount favorite-count">\s*1\s*<\/p>/',
                $response->getContent()
            );
            $this->assertMatchesRegularExpression(
                '/<p class="product-icon-amount">\s*1\s*<\/p>/',
                $response->getContent()
            );
            $response->assertSee($product->description);

            foreach($categories as $category){
                $response->assertSee($category->content);
            }

            $response->assertSee($product->condition->content);

            $response->assertSee('storage/images/' . $this->profile->image);
            $response->assertSee($this->profile->username);
            $response->assertSee($comment->content);
        }
    }

    public function testDetailCategories(){
        $product = Product::find(1);
        $categories = $product->categories;
        $this->assertEquals(count($categories), 3);

        $response = $this->get('/item/' . $product->id);
        foreach ($categories as $category) {
            $response->assertSee($category->content);
        }
    }
}
