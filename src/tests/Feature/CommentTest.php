<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Product;
use App\Models\Comment;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class CommentTest extends TestCase
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
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->seed('UsersTableSeeder');
        $this->seed('ConditionsTableSeeder');
    }

    public function testAuthComment()
    {
        $user1 = User::factory()->create([
            'name' => 'ユーザー1'
        ]);
        $user2 = User::factory()->create([
            'name' => 'ユーザー２'
        ]);

        $this->actingAs($user1);

        $product = Product::create([
            'user_id' => $user2->id,
            'condition_id' => 1,
            'name' => '腕時計',
            'price' => 15000,
            'description' => 'スタイリッシュ',
            'image' => 'watch.jpg',
            'brand' => 'SEIKO',
            'is_purchased' => 0,
        ]);

        $initialCount = Comment::where('product_id', $product->id)->count();

        $response = $this->post("/item/{$product->id}", [
            'content' => 'good'
        ]);

        $this->assertDatabaseHas('comments', [
            'user_id' => $user1->id,
            'product_id' => $product->id,
            'content' => 'good'
        ]);

        $newCount = Comment::where('product_id', $product->id)->count();
        $this->assertEquals($initialCount + 1, $newCount);

        $response->assertRedirect("/item/{$product->id}");
    }

    public function testGuestComment()
    {
        $user1 = User::factory()->create([
            'name' => 'ユーザー1'
        ]);

        $product = Product::create([
            'user_id' => $user1->id,
            'condition_id' => 1,
            'name' => '腕時計',
            'price' => 15000,
            'description' => 'スタイリッシュ',
            'image' => 'watch.jpg',
            'brand' => 'SEIKO',
            'is_purchased' => 0,
        ]);

        $response = $this->post("/item/{$product->id}", [
            'content' => 'good'
        ]);

        $this->assertDatabaseMissing('comments', [
            'product_id' => $product->id,
            'content' => 'good'
        ]);

        $response->assertRedirect('/login');
    }

    public function testAuthInputComment()
    {
        $user1 = User::factory()->create([
            'name' => 'ユーザー1'
        ]);
        $user2 = User::factory()->create([
            'name' => 'ユーザー２'
        ]);

        $this->actingAs($user1);

        $product = Product::create([
            'user_id' => $user2->id,
            'condition_id' => 1,
            'name' => '腕時計',
            'price' => 15000,
            'description' => 'スタイリッシュ',
            'image' => 'watch.jpg',
            'brand' => 'SEIKO',
            'is_purchased' => 0,
        ]);

        $response = $this->post("/item/{$product->id}", [
            'content' => ''
        ]);

        $response->assertSessionHasErrors([
            'content' => 'コメントを入力してください'
        ]);
    }

    public function testAuthInputCommentMax()
    {
        $user1 = User::factory()->create([
            'name' => 'ユーザー1'
        ]);
        $user2 = User::factory()->create([
            'name' => 'ユーザー２'
        ]);

        $this->actingAs($user1);

        $product = Product::create([
            'user_id' => $user2->id,
            'condition_id' => 1,
            'name' => '腕時計',
            'price' => 15000,
            'description' => 'スタイリッシュ',
            'image' => 'watch.jpg',
            'brand' => 'SEIKO',
            'is_purchased' => 0,
        ]);

        $response = $this->post("/item/{$product->id}", [
            'content' => 'あいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえおあいうえお'
        ]);

        $response->assertSessionHasErrors([
            'content' => 'コメントは255文字以下で入力してください'
        ]);
    }
}
