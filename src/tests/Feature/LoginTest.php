<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
    }

    //ログイン機能
    public function test_login_user()
    {
        // ファクトリでユーザー作成
        $user = User::factory()->create([
            'email' => 'general@gmail.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(), // メール認証済みにしておく
        ]);

        $response = $this->post('/login', [
            'email' => "general@gmail.com",
            'password' => "password",
        ]);

        // ログイン後はプロフィール画面へリダイレクトされる仕様に合わせる
        $response->assertRedirect('/mypage/profile');
        $this->assertAuthenticatedAs($user);
    }

    //ログイン--メアドバリデーション
    public function test_login_user_validate_email()
    {
        $response = $this->post('/login', [
            'email' => "",
            'password' => "password",
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');

        $errors = session('errors');
        $this->assertEquals('メールアドレスを入力してください', $errors->first('email'));
    }

    //ログイン--パスワードバリデーション
    public function test_login_user_validate_password()
    {
        $user = User::factory()->create([
            'email' => 'general@gmail.com',
        ]);

        $response = $this->post('/login', [
            'email' => "general@gmail.com",
            'password' => "",
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('password');

        $errors = session('errors');
        $this->assertEquals('パスワードを入力してください', $errors->first('password'));
    }

    //ログイン--不一致
    public function test_login_user_validate_user()
    {
        $user = User::factory()->create([
            'email' => 'general@gmail.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => "general@gmail.com",
            'password' => "password123", // 間違ったパスワード
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');

        $errors = session('errors');
        // メッセージは lang/ja/auth.php に依存する
        $this->assertEquals('ログイン情報が登録されていません', $errors->first('email'));
    }
}
