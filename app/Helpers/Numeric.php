<?php

namespace Helpers;

class Numeric
{

    /** Returns standardized array with formatted, amount, style for negative
     * @param float $number
     * @param int $decimals
     * @param bool $currency
     * @return array
     */
    public static function formatFloat(float $float, int $decimals = 2, bool $currency = false): array
    {

        $amount = round($float, $decimals);
        $formatted = number_format($float, $decimals);
        $style = '';

        if ($amount < 0) {
            $formatted = number_format(abs($float), $decimals);
            $formatted = '(' . $formatted . ')';
            $style     = 'color: red;';
        }
        $formatted = ($currency ? '$': '') . $formatted;

        $data = [
            'amount'    => $amount,
            'formatted' => $formatted,
            'style'     => $style,
        ];

        return $data;
    }


    /**
     * Returns standardized array with formatted currency and amount
     *
     * @param int|float $number
     * @param int $decimals
     * @param string $currency_code
     *
     * @return array
     */
    public static function getCurrencyStandard($number, int $decimals = 0, ?string $currency_code = 'USD'): array
    {
        $amount    = number_format($number, $decimals, '.', '');
        $formatted = number_format($number, $decimals);

        $currency = [
            'amount'    => $amount,
            'formatted' => self::formatCurrency($formatted, $currency_code)
        ];

        return $currency;
    }

    /**
     * @param string $amount
     * @param string $currency_code
     *
     * @return string
     */
    public static function formatCurrency(string $amount, ?string $currency_code = 'USD'): string
    {
        $code = $currency_code != 'USD' ? ' ' . $currency_code : '';

        return '$' . $amount . $code;
    }

    /**
     * Formats a number to start with 0 useful for mobile numbers.
     *
     * @param $number
     * @param string $prefix
     *
     * @return bool|int|mixed|string
     */
    public static function format($number, $prefix = '4')
    {
        //remove any spaces in the number
        $number = str_replace(" ", "", $number);
        $number = trim($number);

        //make sure the number is actually a number
        if (is_numeric($number)) {
            //if number doesn't start with a 0 or a $prefix add a 0 to the start.
            if ($number[0] != 0 && $number[0] != $prefix) {
                $number = "0" . $number;
            }

            //if number starts with a 0 replace with $prefix
            if ($number[0] == 0) {
                $number[0] = str_replace("0", $prefix, $number[0]);
                $number    = $prefix . $number;
            }

            //return the number
            return $number;

            //number is not a number
        } else {
            //return nothing
            return false;
        }
    }

    /**
     * @param $number
     * @param bool $dash
     * @return string
     */
    public static function formatSSN($number, $dash = true): string
    {
        $ssn = preg_replace('/[^0-9]+/', '', $number);
        if ($dash) {
            $ssn = preg_replace('/^(\d{3})(\d{2})(\d{4})$/', '$1-$2-$3', $ssn);
        }

        return $ssn;

    }

    /**
     * @param $input
     *
     * @return int
     */
    public static function isPhone($input): int
    {
        return preg_match('/^((\(\d{3}\) ?)|(\d{3}-))?\d{3}-\d{4}$/', $input);
    }

    /**
     * @param $input
     *
     * @return string
     */
    public static function formatPhone($input): string
    {
        if (strlen($input) == 10) {
            return '(' . substr($input, 0, 3) . ') ' . substr($input, 3, 3) . '-' . substr($input, -4);
        }

        return $input;
    }

    /**
     * @param $input
     *
     * @return mixed
     */
    public static function cleanPhone($input)
    {
        return preg_replace('/\D/', '', $input);
    }

    /**
     * Returns the percentage.
     *
     * @param numeric $val1 start number
     * @param numeric $val2 end number
     *
     * @return string       returns the percentage
     */
    public static function percentage($val1, $val2)
    {
        if ($val1 > 0 && $val2 > 0) {
            $division = $val1 / $val2;
            $res      = $division * 100;

            return round($res) . '%';
        } else {
            return '0%';
        }
    }

    /**
     * @param $input
     *
     * @return int
     */
    public static function isUPC($input): bool
    {
        $even = 0;
        $odd = 0;

        $input = trim($input);

        if (strlen($input) != 12) { return false; } // UPC code always 12 chars long
        if (!is_numeric($input)) { return false; } // must be numeric only

        // check the check digit
        $chars = str_split($input);

        $check = (int)substr($input, -1);

        foreach ($chars as $i => $char) {
            // zero based so 0 is the odd start
            if ($i % 2 == 0) {
                $odd += (int)$char;
            } else if ($i < 11) { // evens skip 12th digit
                $even += (int)$char;
            }
        }

        $sum = ($odd * 3) + $even;

        $validate = (int)((ceil($sum/10))*10) - $sum;

        return $check == $validate;
    }
}
