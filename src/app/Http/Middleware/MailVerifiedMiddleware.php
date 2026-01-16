<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * メール認証チェックミドルウェア
 * 
 * セッションに未認証ユーザー情報がある場合、
 * メール認証画面にリダイレクトする
 */
class MailVerifiedMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // セッションから未認証ユーザー情報を取得
        $sessionData = session()->get('unauthenticated_user') ?? null;

        // 💡 未認証ユーザー情報がセッションに存在する場合、
        // メール認証画面にリダイレクト
        if ($sessionData) {
            return redirect()->route('verification.notice');
        }

        return $next($request);
    }
}
