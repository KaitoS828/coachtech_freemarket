<?php

namespace App\Mail;

use App\Models\Purchase;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CompletedEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */



    // ここが実際にメールを作る場所
    public $purchase; //データを保管する変数を用意

    // 渡されたデータを使う
    public function __construct(Purchase $purchase){
        $this->purchase = $purchase;
    }
    
    public function build(){
        // 件名を「取引完了のお知らせ」などにし、ビューを emails.transaction_completed に指定する
        return $this->subject('取引完了のお知らせ')->view('emails.transaction_completed');
    }


}
