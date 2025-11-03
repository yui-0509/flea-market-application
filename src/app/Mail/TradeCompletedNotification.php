<?php

namespace App\Mail;

use App\Models\Purchase;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TradeCompletedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $purchase;

    public $buyer;

    public function __construct(Purchase $purchase, User $buyer)
    {
        $this->purchase = $purchase;
        $this->buyer = $buyer;
    }

    public function build()
    {
        return $this->subject('取引が完了しました')
            ->view('emails.trade-completed');
    }
}
