<?php

namespace App\Util;

class ExchangeRate
{
    public static float $USD_TO_USD = 1;
    public static float $GBP_TO_USD = 1.3;
    public static float $EUR_TO_USD = 1.08;

    /**
     * Convert GBP to USD
     *
     * @param string $currency
     * @param float $amount
     * @return float
     */
    public static function toUsd(string $currency, float $amount): float
    {
        return match ($currency) {
            'GBP' => $amount * self::$GBP_TO_USD,
            'EUR' => $amount * self::$EUR_TO_USD,
            default => $amount,
        };
    }
}
