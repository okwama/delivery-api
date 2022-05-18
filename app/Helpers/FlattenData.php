<?php
/**
 * Created by PhpStorm.
 * User: Skwea-05
 * Date: 2/19/2020
 * Time: 10:10 AM
 */

namespace App\Helpers;


trait FlattenData
{

    public static $data;

    public static function jsonFormat($data)
    {
        self::$data = $data;
        return json_decode(json_encode(self::$data), false);
    }
}