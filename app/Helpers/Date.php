<?php

namespace Helpers;

use DateInterval;
use DatePeriod;
use DateTime;
use DateTimeZone;
use Exception;

/**
 * Class Date
 *
 * @package Helpers
 */
class Date
{
    /**
     * @param $datetime
     * @param string $format
     *
     * @return false|string
     */
    public static function getDateTimeStandard($datetime, $format = 'M j, Y g:i A'): string
    {
        try {
            if (empty($datetime)) {
                $current_date = new DateTime();
                $datetime     = $current_date->format('Y-m-d H:i:s');
            }

            return date($format, strtotime($datetime));
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param $datetime
     * @param string $format
     *
     * @return string
     */
    public static function getDateStandard($datetime, $format = 'M j, Y'): string
    {
        return self::getDateTimeStandard($datetime, $format);
    }

    /**
     * @param $datetime
     * @param string $format
     *
     * @return false|string
     */
    public static function getTimeStandard($datetime, $format = 'g:i A'): string
    {
        return self::getDateTimeStandard($datetime, $format);
    }

    /**
     * @param $datetime
     * @param string $user_timezone
     * @param string $format
     *
     * @return array
     * @throws \Exception
     */
    public static function getUserDateStandard($datetime, $user_timezone = 'America/Phoenix', $format = 'Y-m-d H:i:s'): string
    {
        if (empty($datetime)) {
            $current_date = new \DateTime();
            $datetime     = $current_date->format($format);
        }

        $user_date = self::dateConvertTimezone($datetime, 'UTC', $user_timezone, $format);

        $date = [
            'utc'  => [
                'datetime' => $datetime
            ],
            'user' => [
                'datetime'       => $user_date,
                'date_formatted' => self::getDateStandard($user_date),
                'time_formatted' => self::getTimeStandard($user_date)
            ]
        ];

        return $date;
    }

    /**
     * @param $preset
     * @param $format
     *
     * @return array|null
     */
    public static function getDateRangeFromPreset($preset, $format = 'Y-m-d'): ?array
    {
        if (!empty($preset)) {

            $start_time = ' 00:00:00';
            $end_time   = ' 23:59:59';

            switch ($preset) {
                case 'last-7-days':
                    $date_start = date($format . $start_time, strtotime('-7 days'));
                    $date_end   = date($format . $end_time);
                    break;
                case 'last-30-days':
                    $date_start = date($format . $start_time, strtotime('-29 days'));
                    $date_end   = date($format . $end_time);
                    break;
                case 'next-7-days':
                    $date_start = date($format . $start_time);
                    $date_end   = date($format . $end_time, strtotime('+7 days'));
                    break;
                case 'next-30-days':
                    $date_start = date($format . $start_time);
                    $date_end   = date($format . $end_time, strtotime('+29 days'));
                    break;
                case 'current-month':
                    $date_start = date('Y-m-01' . $start_time);
                    $date_end   = date('Y-m-t' . $end_time);
                    break;
                case 'next-month':
                    $date_start = date('Y-m-01' . $start_time, strtotime('+1 month'));
                    $date_end   = date('Y-m-t' . $end_time, strtotime('+1 month'));
                    break;
                case 'last-month':
                    $date_start = date('Y-m-01' . $start_time, strtotime('-1 month'));
                    $date_end   = date('Y-m-t' . $end_time, strtotime('-1 month'));
                    break;
                case '2-months-ago':
                    $date_start = date('Y-m-01' . $start_time, strtotime('-2 months'));
                    $date_end   = date('Y-m-t' . $end_time, strtotime('-2 months'));
                    break;
                case '3-months-ago':
                    $date_start = date('Y-m-01' . $start_time, strtotime('-3 months'));
                    $date_end   = date('Y-m-t' . $end_time, strtotime('-3 months'));
                    break;
                case '4-months-ago':
                    $date_start = date('Y-m-01' . $start_time, strtotime('-4 months'));
                    $date_end   = date('Y-m-t' . $end_time, strtotime('-4 months'));
                    break;
                case '5-months-ago':
                    $date_start = date('Y-m-01' . $start_time, strtotime('-5 months'));
                    $date_end   = date('Y-m-t' . $end_time, strtotime('-5 months'));
                    break;
                case '6-months-ago':
                    $date_start = date('Y-m-01' . $start_time, strtotime('-6 months'));
                    $date_end   = date('Y-m-t' . $end_time, strtotime('-6 months'));
                    break;
                default:
                    $preset     = '';
                    $date_start = '';
                    $date_end   = '';
                    break;
            }

            if (!empty($date_start) && !empty($date_end)) {
                $dates = [
                    'date_preset' => $preset,
                    'date_start'  => $date_start,
                    'date_end'    => $date_end
                ];

                return $dates;
            }
        }

        return null;
    }

    /**
     * @param $from
     * @param $to
     * @param null $type
     *
     * @return bool|DateInterval
     * @throws Exception
     */
    public static function difference($from, $to, $type = null)
    {
        $d1 = new DateTime($from);
        if (empty($from)) {
            $d1 = new DateTime();
        }

        $d2 = new DateTime($to);
        if (empty($to)) {
            $d2 = new DateTime();
        }

        $diff = $d2->diff($d1);

        if ($type == null) {
            return $diff;
        } else {
            return $diff->$type;
        }
    }

    /**
     * @param $startDate
     * @param $endDate
     * @param bool $weekendDays
     *
     * @return int
     */
    public static function businessDays($startDate, $endDate, $weekendDays = false)
    {
        $begin = strtotime($startDate);
        $end   = strtotime($endDate);

        if ($begin > $end) {
            return 0;
        } else {
            $numDays  = 0;
            $weekends = 0;

            while ($begin <= $end) {
                $numDays++; // no of days in the given interval
                $whatDay = date('N', $begin);

                if ($whatDay > 5) { // 6 and 7 are weekend days
                    $weekends++;
                }
                $begin += 86400; // +1 day
            };

            if ($weekendDays == true) {
                return $weekends;
            }

            $working_days = $numDays - $weekends;

            return $working_days;
        }
    }

    /**
     * @param $startDate
     * @param $endDate
     * @param int $nonWork
     *
     * @return array
     * @throws Exception
     */
    public static function businessDates($startDate, $endDate, $nonWork = 6)
    {
        $begin     = new DateTime($startDate);
        $end       = new DateTime($endDate);
        $holiday   = [];
        $interval  = new DateInterval('P1D');
        $dateRange = new DatePeriod($begin, $interval, $end);
        foreach ($dateRange as $date) {
            if ($date->format("N") < $nonWork and !in_array($date->format("Y-m-d"), $holiday)) {
                $dates[] = $date->format("Y-m-d");
            }
        }

        return $dates;
    }

    /**
     * @param int $month
     * @param string $year
     *
     * @return int|mixed
     */
    public static function daysInMonth($month = 0, $year = '')
    {
        if ($month < 1 OR $month > 12) {
            return 0;
        } else if (!is_numeric($year) OR strlen($year) !== 4) {
            $year = date('Y');
        }
        if (defined('CAL_GREGORIAN')) {
            return cal_days_in_month(CAL_GREGORIAN, $month, $year);
        }
        if ($year >= 1970) {
            return (int) date('t', mktime(12, 0, 0, $month, 1, $year));
        }
        if ($month == 2) {
            if ($year % 400 === 0 OR ($year % 4 === 0 && $year % 100 !== 0)) {
                return 29;
            }
        }
        $days_in_month = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

        return $days_in_month[$month - 1];
    }

    /**
     * @param $date
     * @param $time
     * @param $format
     *
     * @return string
     */
    public static function concatDateTime($date = '', $time = '', $format = 'Y-m-d H:i:s'): string
    {
        if (empty($date)) {
            $date = date('Y-m-d');
        }

        if (empty($time)) {
            $time = date('H:i:s');
        }

        return (string) date('Y-m-d', strtotime($date)) . ' ' . date('H:i:s', strtotime($time));
    }

    /**
     * @param $datetime
     * @param $timezone_from
     * @param $timezone_to
     * @param string $format
     *
     * @return string|null
     */
    public static function dateConvertTimezone($datetime = '', $timezone_from = 'UTC', $timezone_to = 'America/Phoenix', $format = 'Y-m-d H:i:s')
    {
        if (empty($datetime) || empty($timezone_from) || empty($timezone_to)) {
            return null;
        }

        try {
            $from_datetime = new DateTime($datetime, new DateTimeZone($timezone_from));
            $from_datetime->setTimezone(new DateTimeZone($timezone_to));

            return $from_datetime->format($format);
        } catch (Exception $e) {
            return $e;
        }
    }

    public static function dateConvert($dt, $tz1, $df1, $tz2, $df2)
    {
        // create DateTime object
        $d = DateTime::createFromFormat($df1, $dt, new DateTimeZone($tz1));
        // convert timezone
        $d->setTimezone(new DateTimeZone($tz2));

        // convert date format
        return $d->format($df2);
    }

    /**
     * @param $datetime
     * @param $timezone
     * @param $direction
     * @param $format
     *
     * @return false|string|null
     */
    public static function dateConvertUTC($datetime = '', $timezone = '', $direction = 'to', $format = 'Y-m-d H:i:s')
    {
        if (empty($datetime)) {
            $datetime = date($format);

            if (date_default_timezone_get() == config('timezone', 'UTC')) {
                return $datetime;
            }
        }

        if (empty($timezone)) {
            $timezone = config('user_timezone', 'America/Phoenix');
        }

        if ($direction == 'from') {
            return self::dateConvertTimezone($datetime, 'UTC', $timezone, $format);
        }

        return self::dateConvertTimezone($datetime, $timezone, 'UTC', $format);
    }


    public static function isDate($date): bool
    {
        if ($date != null) {
            $check_date = date('m/d/Y', strtotime($date));
            if (!in_array($check_date, ['12/30/1899', '12/31/1969', '01/01/1970'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $datetime
     * @param string $format
     *
     * @return string|null
     */
    public static function formatDate($datetime, $format = 'Y-m-d H:i:s'): ?string
    {
        if (!Date::isDate($datetime)) {
            return null;
        }

        return date($format, strtotime($datetime));
    }

}
