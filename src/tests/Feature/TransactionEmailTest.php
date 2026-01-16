<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use App\Mail\CompletedEmail;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TransactionEmailTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    // 取引完了時にメールが送信されるテスト
    public function test_transaction_completion_sends_email()
    {
        Mail::fake();

        $buyer = User::first();
        $buyer->markEmailAsVerified();

        $seller = User::where('id', '!=', $buyer->id)->first();
        $seller->markEmailAsVerified();

        $item = Item::where('user_id', $seller->id)->first();

        // 購入データを作成（status=1: 購入者評価済み）
        $purchase = Purchase::create([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'shipping_post_code' => '123-4567',
            'shipping_address' => '東京都渋谷区',
            'shipping_building' => 'テストビル',
            'payment_method' => 'convenience',
            'status' => 1, // 購入者がすでに評価済み
        ]);

        // 出品者が評価を送信（これでstatus=2になる）
        $response = $this->actingAs($seller)->post("/purchase/{$purchase->id}/review", [
            'rate' => 5,
        ]);

        $response->assertSessionHasNoErrors();

        // メールが送信されたか確認
        Mail::assertSent(CompletedEmail::class, 2); // 購入者と出品者の2通

        // 購入者にメールが送信されたか確認
        Mail::assertSent(CompletedEmail::class, function ($mail) use ($buyer) {
            return $mail->hasTo($buyer->email);
        });

        // 出品者にメールが送信されたか確認
        Mail::assertSent(CompletedEmail::class, function ($mail) use ($seller) {
            return $mail->hasTo($seller->email);
        });
    }

    // 購入者が評価した時点ではメールが送信されないテスト
    public function test_email_not_sent_when_only_buyer_reviews()
    {
        Mail::fake();

        $buyer = User::first();
        $buyer->markEmailAsVerified();

        $seller = User::where('id', '!=', $buyer->id)->first();
        $item = Item::where('user_id', $seller->id)->first();

        // 購入データを作成（status=0: 取引中）
        $purchase = Purchase::create([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'shipping_post_code' => '123-4567',
            'shipping_address' => '東京都渋谷区',
            'shipping_building' => 'テストビル',
            'payment_method' => 'convenience',
            'status' => 0,
        ]);

        // 購入者が評価を送信（status=1になる）
        $response = $this->actingAs($buyer)->post("/purchase/{$purchase->id}/review", [
            'rate' => 5,
        ]);

        $response->assertSessionHasNoErrors();

        // ステータスが1になっているか確認
        $purchase->refresh();
        $this->assertEquals(1, $purchase->status);

        // メールは送信されていないことを確認
        Mail::assertNotSent(CompletedEmail::class);
    }

    // メールの内容が正しいか確認するテスト
    public function test_email_contains_correct_information()
    {
        Mail::fake();

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
            'status' => 1,
        ]);

        // 出品者が評価を送信
        $this->actingAs($seller)->post("/purchase/{$purchase->id}/review", [
            'rate' => 5,
        ]);

        // メールが送信され、正しいPurchaseオブジェクトが含まれているか確認
        Mail::assertSent(CompletedEmail::class, function ($mail) use ($purchase) {
            return $mail->purchase->id === $purchase->id;
        });
    }
}
