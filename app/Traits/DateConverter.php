<?php


namespace App\Traits;


use DateTime;
use Exception;
use MongoDB\BSON\UTCDateTime;

trait DateConverter
{
    /**
     * @param $date
     * @return UTCDateTime
     * @throws Exception
     */
    public function convert($date)
    {
        return new UTCDateTime(new DateTime($date));
    }
}
