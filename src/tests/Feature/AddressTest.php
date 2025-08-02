<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Product;
use App\Models\Profile;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Faker\Factory as FakerFactory;
use Faker\Generator;

class AddressTest extends TestCase
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
    }


    public function testAdress()
    {
        $product = Product::find(1);
        $this->actingAs($this->user);
        $response = $this->get('/purchase/address/'. $product->id);
        $response->assertStatus(200);

        $response = $this->post('/purchase/address/'. $product->id, [
            'postal_code' => 1234567,
            'address' => '山梨県田中市中央区杉山町田辺1-5-3',
            'building' => 'コーポ吉本108号'
        ]);

        $response->assertRedirect('/purchase/' . $product->id);

        $response = $this->get('/purchase/' . $product->id);
        $response->assertStatus(200);
        $response->assertSee(1234567);
        $response->assertSee('山梨県田中市中央区杉山町田辺1-5-3');
        $response->assertSee('コーポ吉本108号');
    }

    public function testStoreUpdatedAddress(){
        $this->actingAs($this->user);
        $product = Product::find(1);

        $response = $this->get(route('purchase.form', ['item_id' => $product->id]));
        $response->assertStatus(200);

        $response = $this->get('/purchase/address/' . $product->id);
        $response->assertStatus(200);

        $response = $this->post('/purchase/address/' . $product->id, [
            'postal_code' => 1234567,
            'address' => '山梨県田中市中央区杉山町田辺1-5-3',
            'building' => 'コーポ吉本108号'
        ]);

        $response->assertRedirect('/purchase/' . $product->id);

        $response = $this->get('/purchase/' . $product->id);
        $response->assertStatus(200);
        $response->assertSee(1234567);
        $response->assertSee('山梨県田中市中央区杉山町田辺1-5-3');
        $response->assertSee('コーポ吉本108号');

        $session = $this->app['session.store'];

        $postal_code = $session->get('purchase_postal_code');
        $address = $session->get('purchase_address');
        $building = $session->get('purchase_building');

        $response = $this->post('/purchase/checkout', [
            'product_id' => $product->id,
            'price' => $product->price,
            'postal_code' => $postal_code,
            'address' => $address,
            'building' => $building,
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
            'postal_code' => '1234567',
            'address' => '山梨県田中市中央区杉山町田辺1-5-3',
            'building' => 'コーポ吉本108号',
        ]);
    }
}
