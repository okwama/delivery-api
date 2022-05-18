<?php

namespace App\Jobs;

use App\Mail\ClientOrder;
use App\Mail\OrderReceived;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $email_order;

    /**
     * Create a new job instance.
     *
     * @param $email_order
     */
    public function __construct($email_order)
    {
        $this->email_order=$email_order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // send to admin
        Mail::to(config('mail.from.address'))
            ->cc(config('mail.from.cc'))
            ->send(new OrderReceived($this->email_order));
        // send to client
        Mail::to($this->email_order['customer_email'])
            ->send(new ClientOrder($this->email_order));
    }
}
