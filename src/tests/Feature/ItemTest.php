<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use App\Models\Like;
use App\Models\Comment;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ItemTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    //商品一覧
    public function test_get_items()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $this->assertCount(10, Item::all());
    }

    //mylist取得
    public function test_get_mylist(){
        $user = User::first();
        // ★修正: メール認証済みに設定
        $user->markEmailAsVerified();

        $item = Item::where('user_id', '!=', $user->id)->first();
        
        Like::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->actingAs($user)->get('/?tab=mylist');

        $response->assertStatus(200);
        $response->assertSee($item->name);
    }

    //商品検索
    public function test_search_item(){
        $response = $this->get('/?keyword=腕時計');
        $response->assertStatus(200);
        $response->assertSee('腕時計');
        $response->assertDontSee('ノートPC');
    }

    //商品詳細情報取得
    public function test_item_detail(){
        $item = Item::first();
        $response = $this->get('/item/' . $item->id);

        $response->assertStatus(200);
        $response->assertSee($item->name);
        $response->assertSee(number_format($item->price));
        $response->assertSee($item->description);
        $response->assertSee($item->condition->condition);
        foreach($item->categories as $category){
            $response->assertSee($category->name);
        }
    }

    //いいね機能
    public function test_like_item(){
        $user = User::first();
        // ★修正: メール認証済みに設定
        $user->markEmailAsVerified(); 

        $item = Item::skip(1)->first(); // 2番目の商品

        // CSRF無効化はFeatureテストで通常自動だが、念のため消しておく（Laravel設定による）
        // $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $response = $this->actingAs($user)->post(route('like.toggle', ['itemId' => $item->id]));

        $response->assertStatus(302);
        
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id
        ]);
        
        // 解除
        $response = $this->actingAs($user)->post(route('like.toggle', ['itemId' => $item->id]));
        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id
        ]);
    }

    //コメント送信機能
    public function test_add_comment(){
        $user = User::first();
        // ★修正: メール認証済みに設定
        $user->markEmailAsVerified(); 

        $item = Item::first();

        $response = $this->actingAs($user)->post(route('comment.store'),[
            'item_id' => $item->id,
            'comment' => 'テストコメントです。'
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('comments',[
            'user_id' => $user->id,
            'item_id' => $item->id,
            'comment' => 'テストコメントです。'
        ]);
    }
}
