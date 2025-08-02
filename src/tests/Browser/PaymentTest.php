<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Product;
use App\Models\Profile;
use Tests\DuskTestCase;
use Illuminate\Support\Facades\DB;
use Faker\Factory as FakerFactory;
use Faker\Generator;

class PaymentTest extends DuskTestCase
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

    public function testPayment()
    {
        $product = Product::find(1);
        $this->browse(function (Browser $browser){
            $browser->loginAs($this->user)->visit('/purchase/1')->select('payment', '1')->waitForText('カード支払い',5)->assertSeeIn('#selected-payment-method', 'カード支払い')
            ->select('payment', '0')
            ->waitForText('コンビニ払い', 5)
            ->assertSeeIn('#selected-payment-method', 'コンビニ払い');
        });
    }
}
