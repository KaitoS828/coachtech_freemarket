<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Review;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    // 購入者が評価を投稿するテスト
    public function test_buyer_can_submit_review()
    {
        $buyer = User::first();
        $buyer->markEmailAsVerified();

        $seller = User::where('id', '!=', $buyer->id)->first();
        $item = Item::where('user_id', $seller->id)->first();

        $purchase = Purchase::create([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'shipping_post_code' => '123-4567',
            'shipping_address' => '東京都渋谷区',
            'shipping_building' => 'テストビル',
            'payment_method' => 'convenience',
            'status' => 0, // 初期状態
        ]);

        $response = $this->actingAs($buyer)->post("/purchase/{$purchase->id}/review", [
            'rate' => 5,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('item.index'));

        // 評価がDBに保存されているか確認
        $this->assertDatabaseHas('reviews', [
            'purchase_id' => $purchase->id,
            'user_id' => $buyer->id,
            'receiver_id' => $seller->id,
            'rate' => 5,
        ]);

        // ステータスが1（購入者評価済み）になっているか確認
        $purchase->refresh();
        $this->assertEquals(1, $purchase->status);
    }

    // 出品者が評価を投稿するテスト
    public function test_seller_can_submit_review()
    {
        $buyer = User::first();
        $buyer->markEmailAsVerified();

        $seller = User::where('id', '!=', $buyer->id)->first();
        $seller->markEmailAsVerified();

        $item = Item::where('user_id', $seller->id)->first();

        $purchase = Purchase::create([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'shipping_post_code' => '123-4567',
            'shipping_address' => '東京都渋谷区',
            'shipping_building' => 'テストビル',
            'payment_method' => 'convenience',
            'status' => 1, // 購入者が評価済み
        ]);

        $response = $this->actingAs($seller)->post("/purchase/{$purchase->id}/review", [
            'rate' => 4,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('item.index'));

        // 評価がDBに保存されているか確認
        $this->assertDatabaseHas('reviews', [
            'purchase_id' => $purchase->id,
            'user_id' => $seller->id,
            'receiver_id' => $buyer->id,
            'rate' => 4,
        ]);

        // ステータスが2（取引完了）になっているか確認
        $purchase->refresh();
        $this->assertEquals(2, $purchase->status);
    }

    // ステータス遷移テスト（0→1→2）
    public function test_purchase_status_transition()
    {
        $buyer = User::first();
        $buyer->markEmailAsVerified();

        $seller = User::where('id', '!=', $buyer->id)->first();
        $seller->markEmailAsVerified();

        $item = Item::where('user_id', $seller->id)->first();

        // 初期状態: status = 0
        $purchase = Purchase::create([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'shipping_post_code' => '123-4567',
            'shipping_address' => '東京都渋谷区',
            'shipping_building' => 'テストビル',
            'payment_method' => 'convenience',
            'status' => 0,
        ]);

        $this->assertEquals(0, $purchase->status);

        // 購入者が評価 → status = 1
        $this->actingAs($buyer)->post("/purchase/{$purchase->id}/review", [
            'rate' => 5,
        ]);

        $purchase->refresh();
        $this->assertEquals(1, $purchase->status);

        // 出品者が評価 → status = 2
        $this->actingAs($seller)->post("/purchase/{$purchase->id}/review", [
            'rate' => 4,
        ]);

        $purchase->refresh();
        $this->assertEquals(2, $purchase->status);
    }

    // 評価平均の計算テスト
    public function test_calculate_average_rating()
    {
        $user = User::first();
        $user->markEmailAsVerified();

        $otherUser1 = User::where('id', '!=', $user->id)->first();
        $otherUser2 = User::where('id', '!=', $user->id)->skip(1)->first();

        // ダミーの購入データを作成
        $purchase1 = Purchase::create([
            'user_id' => $otherUser1->id,
            'item_id' => 1,
            'shipping_post_code' => '123-4567',
            'shipping_address' => '東京都渋谷区',
            'payment_method' => 'convenience',
            'status' => 2,
        ]);

        $purchase2 = Purchase::create([
            'user_id' => $otherUser2->id,
            'item_id' => 2,
            'shipping_post_code' => '123-4567',
            'shipping_address' => '東京都渋谷区',
            'payment_method' => 'convenience',
            'status' => 2,
        ]);

        // ユーザーへの評価を作成（5と3）
        Review::create([
            'purchase_id' => $purchase1->id,
            'user_id' => $otherUser1->id,
            'receiver_id' => $user->id,
            'rate' => 5,
        ]);

        Review::create([
            'purchase_id' => $purchase2->id,
            'user_id' => $otherUser2->id,
            'receiver_id' => $user->id,
            'rate' => 3,
        ]);

        // 平均値を計算（5+3）/ 2 = 4
        $averageRating = Review::where('receiver_id', $user->id)->avg('rate');
        $this->assertEquals(4, $averageRating);

        // 四捨五入のテスト（3つの評価: 5, 4, 3 → 平均4）
        Review::create([
            'purchase_id' => $purchase1->id,
            'user_id' => $otherUser1->id,
            'receiver_id' => $user->id,
            'rate' => 4,
        ]);

        $averageRating = Review::where('receiver_id', $user->id)->avg('rate');
        $roundedRating = round($averageRating);
        $this->assertEquals(4, $roundedRating);
    }

    // バリデーションエラーテスト（評価が必須）
    public function test_review_validation_rate_required()
    {
        $buyer = User::first();
        $buyer->markEmailAsVerified();

        $seller = User::where('id', '!=', $buyer->id)->first();
        $item = Item::where('user_id', $seller->id)->first();

        $purchase = Purchase::create([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'shipping_post_code' => '123-4567',
            'shipping_address' => '東京都渋谷区',
            'shipping_building' => 'テストビル',
            'payment_method' => 'convenience',
            'status' => 0,
        ]);

        $response = $this->actingAs($buyer)->post("/purchase/{$purchase->id}/review", [
            'rate' => null,
        ]);

        $response->assertSessionHasErrors('rate');
    }

    // バリデーションエラーテスト（評価の範囲）
    public function test_review_validation_rate_range()
    {
        $buyer = User::first();
        $buyer->markEmailAsVerified();

        $seller = User::where('id', '!=', $buyer->id)->first();
        $item = Item::where('user_id', $seller->id)->first();

        $purchase = Purchase::create([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'shipping_post_code' => '123-4567',
            'shipping_address' => '東京都渋谷区',
            'shipping_building' => 'テストビル',
            'payment_method' => 'convenience',
            'status' => 0,
        ]);

        // 6以上の評価
        $response = $this->actingAs($buyer)->post("/purchase/{$purchase->id}/review", [
            'rate' => 6,
        ]);
        $response->assertSessionHasErrors('rate');

        // 0以下の評価
        $response = $this->actingAs($buyer)->post("/purchase/{$purchase->id}/review", [
            'rate' => 0,
        ]);
        $response->assertSessionHasErrors('rate');
    }
}
