<?php


namespace App\Helpers;


class GeneralHelper
{
    /*
     * Generate random string
     */
    public static function generateRandomString($length = 8)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    /*
      * Generate random integer
      */
    public static function generateRandomInteger($length = 5)
    {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    /**
     * Format phoneNumber and remove whitespaces
     * @param  string  $phoneNumber
     * @return string|string[]
     */
    public static function trimPhoneNumber(string $phoneNumber)
    {
       return  str_replace(' ', '', $phoneNumber);
    }
        //Clean string
    public static function cleanString($string) {
        $string = str_replace(' ', ' ', $string); // Replaces all spaces with hyphens.

        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }
}
