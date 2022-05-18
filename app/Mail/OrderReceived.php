<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class OrderReceived extends Mailable
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
        $admin = config("mail.from.name");
        return $this
            ->subject('New Order')
            ->from(config("mail.from.address"), $this->order['customer_name'])
            ->markdown('emails.order-received',[
                'order' => $this->order,
                'admin' => $admin,
            ]);
    }
}
