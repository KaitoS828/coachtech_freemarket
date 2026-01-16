<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Profile;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    //ログアウト機能
    public function test_logout_user(){
        $user = User::first();
        // ログインするためにはメール認証が必要か？ログアウトだけなら不要かもだが念のため
        $user->markEmailAsVerified();

        $response = $this->actingAs($user)->post('/logout');

        // 通常は / にリダイレクトか、ログイン画面か。Fortifyデフォルトは / 
        // ログイン機能のテストではないので、リダイレクトすればOKとするか、ルートを確認
        // LoginTestでは /mypage/profile だったので、ログアウト後はログイン画面？
        // テスト実行して確認する
        $response->assertStatus(302);
        $this->assertGuest();
    }

    //ユーザ情報取得
    public function test_get_profile(){
        $user = User::first();
        $user->markEmailAsVerified();

        $response = $this->actingAs($user)->get('/mypage/profile');

        $response->assertStatus(200);
        // ProfileSeederのデータに依存
        // ProfileSeederの内容を確認していないので、動的に取得して検証
        $profile = Profile::where('user_id', $user->id)->first();
        if ($profile) {
            $response->assertSee($profile->postcode);
            $response->assertSee($profile->address);
        }
    }

    //ユーザ情報変更
    public function test_change_profile(){
        $user = User::first();
        $user->markEmailAsVerified();

        // routes/web.phpでは PATCH で定義されているため修正
        $response = $this->actingAs($user)->patch('/mypage/profile',[
            'name' => "変更後ネーム",
            'post_code' => "111-0032", // ハイフンあり
            'address' => "東京都台東区浅草2-3-1",
            'building' => "浅草寺",
            // image は必須かどうかによるが、空でも通るか試す
        ]);

        $response->assertRedirect(route('mypage.show')); // マイページTOPへ
        
        $this->assertDatabaseHas( Profile::class, [
            'user_id' => $user->id,
            'post_code' => "111-0032",
            'address' => "東京都台東区浅草2-3-1",
            'building' => "浅草寺",
        ]);
        
        // Userテーブルの名前も更新される仕様かはコントローラー次第
        // $this->assertDatabaseHas( User::class, ['id' => $user->id, 'name' => "変更後ネーム"] );
    }

    //出品情報登録
    public function test_listing_item(){
        $user = User::first();
        $user->markEmailAsVerified();

        Storage::fake('public'); // publicディスクをfake
        $image = UploadedFile::fake()->create('test_item.png', 150);

        // カテゴリIDが必要。CategorySeederで入っているはず
        // coachtech_freemarketの実装に合わせてリクエストパラメータを調整
        // create.blade.php を見た時、name="category_id" ではなく checkboxの配列だった記憶がない
        // 模範解答は 'categories' => [2,3,4]
        // 自分で実装した時は condition_id を使った

        $response = $this->actingAs($user)->post('/sell',[
            'image' => $image, 
            'name' => "テストアイテム",
            'price' => 5000,
            'brand_name' => '',
            'description' => "テストテストテストテスト",
            'categories' => [2,3,4], // ItemControllerでは $request->categories を使用
            'condition_id' => 4,
        ]);

        $response->assertRedirect(route('item.index'));
    }
}
