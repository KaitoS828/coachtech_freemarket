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

        // 購入者に送信
        Mail::to($purchase->user->email)->send(new CompletedEmail($purchase) );

        // 出品者に送信
        Mail::to($purchase->item->user->email)  ->send(new CompletedEmail($purchase));
    }
}
