<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Product;
use App\Models\Profile;
use Stripe\Checkout\Session as StripeSession;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Faker\Factory as FakerFactory;
use Faker\Generator;
use Mockery;

class PurchaseTest extends TestCase
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
        DB::table('conditions')->truncate();
        DB::table('products')->truncate();
        DB::table('users')->truncate
        ();
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
    }

    public function testPurchaseAtKonbini()
    {
        $this->actingAs($this->user);
        $product = Product::find(1);

        $response = $this->get(route('purchase.form', ['item_id' => $product->id]));
        $response->assertStatus(200);

        //コンビニ払い
        $response = $this->post('/purchase/checkout', [
            'product_id' => $product->id,
            'price' => $product->price,
            'postal_code' => $this->user->profile->postal_code,
            'address' => $this->user->profile->address,
            'building' => $this->user->profile->building,
            'payment' => '0',
        ]);

        $redirectUrl = $response->headers->get('Location');
        $this->assertTrue(str_starts_with($redirectUrl, url('/purchase/success')));

        $redirectUrl = $response->headers->get('Location');

        $response = $this->get($redirectUrl);
        $response->assertRedirect('/');

        $this->assertDatabaseHas('purchases', [
            'user_id' => $this->user->id,
            'product_id' => $product->id,
            'payment' => 0,
            'postal_code' => $this->user->profile->postal_code,
            'address' => $this->user->profile->address,
            'building' => $this->user->profile->building,
        ]);
        
    }

    public function testPurchaseAtCard(){
        $this->actingAs($this->user);
        $product = Product::find(1);
        $this->get(route('purchase.form', ['item_id' => $product->id]))->assertStatus(200);

        $mock = Mockery::mock('alias:' . StripeSession::class);
        $mock->shouldReceive('create')
            ->once()
            ->andReturn((object)[
                'url' => 'https://checkout.stripe.com/pay/cs_test_mocked',
            ]);
        $mock->shouldReceive('retrieve')
            ->once()
            ->andReturn((object)[
                'payment_status' => 'paid',
                'metadata' => (object)[
                    'user_id' => $this->user->id,
                    'product_id' => $product->id,
                ],
            ]);

        $response = $this->post('/purchase/checkout', [
            'product_id' => $product->id,
            'price' => $product->price,
            'postal_code' => $this->user->profile->postal_code,
            'address' => $this->user->profile->address,
            'building' => $this->user->profile->building,
            'payment' => '1',
        ]);

        $response->assertRedirect('https://checkout.stripe.com/pay/cs_test_mocked');

        $response = $this->withSession([
            'purchase_postal_code' => $this->user->profile->postal_code,
            'purchase_address' => $this->user->profile->address,
            'purchase_building' => $this->user->profile->building,
        ])->call('GET', '/purchase/success', [
            'session_id' => 'cs_test_mocked',
        ]);

        $response->assertRedirect('/');

        $this->assertDatabaseHas('purchases', [
            'user_id' => $this->user->id,
            'product_id' => $product->id,
            'payment' => 1,
            'postal_code' => $this->user->profile->postal_code,
            'address' => $this->user->profile->address,
            'building' => $this->user->profile->building,
        ]);
    }

    public function testPurchasedProduct(){
        $this->actingAs($this->user);
        $product = Product::find(1);

        $response = $this->get(route('purchase.form', ['item_id' => $product->id]));
        $response->assertStatus(200);

        //コンビニ払い
        $response = $this->post('/purchase/checkout', [
            'product_id' => $product->id,
            'price' => $product->price,
            'postal_code' => $this->user->profile->postal_code,
            'address' => $this->user->profile->address,
            'building' => $this->user->profile->building,
            'payment' => '0',
        ]);

        $redirectUrl = $response->headers->get('Location');
        $this->assertTrue(str_starts_with($redirectUrl, url('/purchase/success')));

        $redirectUrl = $response->headers->get('Location');

        $response = $this->get($redirectUrl);
        $response->assertRedirect('/');

        $response = $this->get('/');
        $response->assertStatus(200);

        $response->assertSeeInOrder([
            $product->name,
            'sold'
        ]);
    }

    public function testPurchaseProfile(){
        $this->actingAs($this->user);
        $product = Product::find(1);

        $response = $this->get(route('purchase.form', ['item_id' => $product->id]));
        $response->assertStatus(200);

        //コンビニ払い
        $response = $this->post('/purchase/checkout', [
            'product_id' => $product->id,
            'price' => $product->price,
            'postal_code' => $this->user->profile->postal_code,
            'address' => $this->user->profile->address,
            'building' => $this->user->profile->building,
            'payment' => '0',
        ]);

        $redirectUrl = $response->headers->get('Location');
        $this->assertTrue(str_starts_with($redirectUrl, url('/purchase/success')));

        $redirectUrl = $response->headers->get('Location');

        $response = $this->get($redirectUrl);
        $response->assertRedirect('/');

        $response = $this->get('/mypage?tab=buy');
        $response->assertStatus(200);
        $response->assertSeeInOrder([
            $product->name,
            'sold'
        ]);
    }
}
