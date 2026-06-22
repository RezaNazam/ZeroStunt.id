<?php

class NumberHelper
{
    public static function decimal($value, int $precision = 1): string
    {
        if ($value === null || $value === '') {
            return '-';
        }

        $number = (float) $value;

        // hasil contoh: 12.0, 12.3, 12.7
        $formatted = number_format($number, $precision, '.', '');

        // hapus .0 kalau belakangnya nol
        return rtrim(rtrim($formatted, '0'), '.');
    }
}