<?php


namespace App\Traits;

use AfricasTalking\SDK\AfricasTalking;

trait AfricasTalkingSMS
{

    private $userName = 'nairobidrinks';
    private $APIKey = '043f5001cc5ad01b455ec01068fad10a49489a2bab1b3822a1e8307c42334877';
    private $from = '';

    // Get the sms object
    public function SMS()
    {
        $AT = new AfricasTalking($this->userName, $this->APIKey);
        return $AT->sms();
    }

// SMS sender ID
    public function sendId()
    {
        return $this->from;
    }
}

