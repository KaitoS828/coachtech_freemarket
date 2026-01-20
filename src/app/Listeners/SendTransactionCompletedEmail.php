<?php

namespace App\Listeners;

use App\Events\TransactionCompletedEvent;

use Illuminate\Support\Facades\Mail;
use App\Mail\CompletedEmail;


class SendTransactionCompletedEmail
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\TransactionCompletedEvent  $event
     * @return void
     */
    public function handle(TransactionCompletedEvent $event)
    {
        $purchase = $event->purchase;

        // 出品者のみに送信（評価シート要件：「商品出品者宛に」通知メールを送信）
        Mail::to($purchase->item->user->email)->send(new CompletedEmail($purchase));
    }
}
