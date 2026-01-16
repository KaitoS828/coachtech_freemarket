<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    //商品購入機能
    public function test_purchase_item(){
        $user = User::first();
        $user->markEmailAsVerified(); // メール認証済み

        // 自分が出品していない商品を選ぶ
        // 購入対象の商品ID取得（自分の商品以外）
        $item = Item::where('user_id', '!=', $user->id)->first();
        
        // 購入前に住所情報が必要（PurchaseControllerの仕様）
        // UserSeederで作成されたユーザーにProfileSeederで住所が入っているはず
        // ProfileSeederを確認していないが、なければ作成する
        if (!$user->profile) {
            $user->profile()->create([
                'post_code' => '123-4567',
                'address' => '東京都渋谷区',
                'building' => 'テストビル'
            ]);
        }

        $response = $this->actingAs($user)->post("/purchase/{$item->id}",[
            'payment_method' => 'convenience', // コンビニ払い (即時完了フロー)
            // 配送先住所は自動的にプロフィールから使用されるためパラメータ不要
        ]);

        $response->assertSessionHasNoErrors();

        // 購入完了後、商品一覧へリダイレクト
        $response->assertRedirect(route('item.index'));
        
        // DBで購入履歴を確認
        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => 'convenience',
        ]);
        
        // 商品がSOLDになっているか確認
        // $this->assertDatabaseHas('items', ['id' => $item->id, 'is_sold' => true]); 
        // booleanは0/1で保存されるためtrue/1どちらでも通るはずだが、念のため
        $item->refresh();
        $this->assertTrue((bool)$item->is_sold);
    }
}
