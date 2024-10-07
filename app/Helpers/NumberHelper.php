<?php

namespace App\Helpers;

class NumberHelper
{

    /**
     * Format any pricing to two decimal places
     * @param $number
     * @return string
     */
    public static function formatToPricing($number): string
    {
        return number_format($number, 2);
    }

}
