<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class ClientOrder extends Mailable
{
    public $order;

    /**
     * Create a new message instance.
     *
     * @param array $order
     */
    public function __construct(array $order)
    {
        $this->order = $order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    { 
        return $this
            ->subject('Order Received')
            ->from(config("mail.from.address"), config("mail.from.name"))
            ->markdown('emails.client-order',[
                'order' => $this->order,
            ]);
    }
}
