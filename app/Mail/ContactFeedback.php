<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class ContactFeedback extends Mailable
{
    public $contact;
    public function __construct(array $contact)
    {
        $this->contact=$contact;
    }

    public function build()
    {
        $admin = config("mail.from.name");
        return $this
            ->subject('Customer Feedback')
            ->from(config('mail.from.address'),$this->contact['name'])
            ->markdown('emails.contact-feedback',[
                'contact' => $this->contact,
                'admin' => $admin,
            ]);
    }
}
