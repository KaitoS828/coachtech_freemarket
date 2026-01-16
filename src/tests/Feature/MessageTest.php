<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Message;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MessageTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    // メッセージ送信テスト（本文のみ）
    public function test_send_message_with_content_only()
    {
        $user = User::first();
        $user->markEmailAsVerified();

        // 購入データを作成
        $item = Item::where('user_id', '!=', $user->id)->first();
        $purchase = Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'shipping_post_code' => '123-4567',
            'shipping_address' => '東京都渋谷区',
            'shipping_building' => 'テストビル',
            'payment_method' => 'convenience',
            'status' => 0,
        ]);

        $response = $this->actingAs($user)->post("/message/{$purchase->id}", [
            'content' => 'テストメッセージです',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('message.show', ['purchaseId' => $purchase->id]));

        // DBにメッセージが保存されているか確認
        $this->assertDatabaseHas('messages', [
            'purchase_id' => $purchase->id,
            'user_id' => $user->id,
            'content' => 'テストメッセージです',
        ]);
    }

    // メッセージ送信テスト（画像付き）
    public function test_send_message_with_image()
    {
        Storage::fake('public');

        $user = User::first();
        $user->markEmailAsVerified();

        $item = Item::where('user_id', '!=', $user->id)->first();
        $purchase = Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'shipping_post_code' => '123-4567',
            'shipping_address' => '東京都渋谷区',
            'shipping_building' => 'テストビル',
            'payment_method' => 'convenience',
            'status' => 0,
        ]);

        // GDライブラリなしでファイルを作成
        $image = UploadedFile::fake()->create('test.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($user)->post("/message/{$purchase->id}", [
            'content' => 'テストメッセージです',
            'image' => $image,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('message.show', ['purchaseId' => $purchase->id]));

        // 画像が保存されているか確認
        $message = Message::where('purchase_id', $purchase->id)->first();
        $this->assertNotNull($message->image_path);
        Storage::disk('public')->assertExists($message->image_path);
    }

    // バリデーションエラーテスト（本文未入力）
    public function test_message_validation_content_required()
    {
        $user = User::first();
        $user->markEmailAsVerified();

        $item = Item::where('user_id', '!=', $user->id)->first();
        $purchase = Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'shipping_post_code' => '123-4567',
            'shipping_address' => '東京都渋谷区',
            'shipping_building' => 'テストビル',
            'payment_method' => 'convenience',
            'status' => 0,
        ]);

        $response = $this->actingAs($user)->post("/message/{$purchase->id}", [
            'content' => '',
        ]);

        $response->assertSessionHasErrors('content');
    }

    // バリデーションエラーテスト（文字数超過）
    public function test_message_validation_content_max()
    {
        $user = User::first();
        $user->markEmailAsVerified();

        $item = Item::where('user_id', '!=', $user->id)->first();
        $purchase = Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'shipping_post_code' => '123-4567',
            'shipping_address' => '東京都渋谷区',
            'shipping_building' => 'テストビル',
            'payment_method' => 'convenience',
            'status' => 0,
        ]);

        $response = $this->actingAs($user)->post("/message/{$purchase->id}", [
            'content' => str_repeat('あ', 401), // 401文字
        ]);

        $response->assertSessionHasErrors('content');
    }

    // バリデーションエラーテスト（画像形式違反）
    public function test_message_validation_image_mimes()
    {
        Storage::fake('public');

        $user = User::first();
        $user->markEmailAsVerified();

        $item = Item::where('user_id', '!=', $user->id)->first();
        $purchase = Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'shipping_post_code' => '123-4567',
            'shipping_address' => '東京都渋谷区',
            'shipping_building' => 'テストビル',
            'payment_method' => 'convenience',
            'status' => 0,
        ]);

        $image = UploadedFile::fake()->create('test.pdf', 100);

        $response = $this->actingAs($user)->post("/message/{$purchase->id}", [
            'content' => 'テストメッセージです',
            'image' => $image,
        ]);

        $response->assertSessionHasErrors('image');
    }

    // メッセージ編集テスト
    public function test_edit_message()
    {
        $user = User::first();
        $user->markEmailAsVerified();

        $item = Item::where('user_id', '!=', $user->id)->first();
        $purchase = Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'shipping_post_code' => '123-4567',
            'shipping_address' => '東京都渋谷区',
            'shipping_building' => 'テストビル',
            'payment_method' => 'convenience',
            'status' => 0,
        ]);

        $message = Message::create([
            'purchase_id' => $purchase->id,
            'user_id' => $user->id,
            'content' => '元のメッセージ',
        ]);

        $response = $this->actingAs($user)->patch("/message/{$purchase->id}/{$message->id}", [
            'content' => '編集後のメッセージ',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('message.show', ['purchaseId' => $purchase->id]));

        // DBが更新されているか確認
        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'content' => '編集後のメッセージ',
        ]);
    }

    // メッセージ削除テスト
    public function test_delete_message()
    {
        $user = User::first();
        $user->markEmailAsVerified();

        $item = Item::where('user_id', '!=', $user->id)->first();
        $purchase = Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'shipping_post_code' => '123-4567',
            'shipping_address' => '東京都渋谷区',
            'shipping_building' => 'テストビル',
            'payment_method' => 'convenience',
            'status' => 0,
        ]);

        $message = Message::create([
            'purchase_id' => $purchase->id,
            'user_id' => $user->id,
            'content' => '削除するメッセージ',
        ]);

        $response = $this->actingAs($user)->delete("/message/{$purchase->id}/{$message->id}");

        $response->assertRedirect(route('message.show', ['purchaseId' => $purchase->id]));

        // DBから削除されているか確認
        $this->assertDatabaseMissing('messages', [
            'id' => $message->id,
        ]);
    }
}
