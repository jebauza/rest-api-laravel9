<?php

namespace App\Helpers;

use Throwable;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Mail\ExceptionOccuredMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

class Utils
{
    /**
     * Method dumpLog
     *
     * @param String $message
     * @param String $type
     *
     * @return void
     */
    public static function dumpLog(String $message, String $type = 'info')
    {
        dump($message);
        Log::$type($message);
    }

    /**
     * Method logElapsedTime
     *
     * @param $timeStart $timeStart
     * @param String $task
     *
     * @return void
     */
    public static function logElapsedTime($timeStart, String $task = "Elapsed Time")
    {
        $now = Carbon::now();
        $elapsedTime = $now->diffInMilliseconds($timeStart);
        Utils::dumpLog($task . ': ' . gmdate("H:i:s", $elapsedTime / 1000) . " (" . $elapsedTime . ")");
    }

    /**
     * Method message
     *
     * @param string $message [explicite description]
     *
     * @return void
     */
    public static function message(string $message)
    {
        dump($message);
        Log::info($message);
    }

    /**
     * Method calculatePercentage
     *
     * @param float|null $maxValue
     * @param float $currentValue
     *
     * @return float
     */
    public static function calculatePercentage(float $maxValue = null, float $currentValue)
    {
        if (empty($maxValue)) {
            return 0;
        }
        return round(($currentValue * 100) / $maxValue, 2);
    }

    /**
     * Method calculateAverage
     *
     * @param float|null $quantity
     * @param float $total
     *
     * @return float
     */
    public static function calculateAverage(float $quantity = null, float $total)
    {
        if (empty($quantity)) {
            return 0;
        }
        return round($total / $quantity, 2);
    }

    /**
     * Method convertToPositiveNumber
     *
     * @param float $number
     *
     * @return float
     */
    public static function convertToPositiveNumber(float $number)
    {
        if (empty($number)) {
            return 0;
        }

        return $number >= 0 ? $number : (-1*$number);
    }

    /**
     * Method secureDivision
     *
     * @param float $numerator
     * @param float|null $denominator
     *
     * @return float
     */
    public static function secureDivision(float $numerator, float $denominator = null)
    {
        if ($denominator === 0 || is_null($denominator)) {
            return 0;
        }

        return $numerator / $denominator;
    }

    /**
     * Method timeInterval
     *
     * @param string $from
     * @param string $to
     *
     * @return array
     */
    public static function timeInterval(string $from, string $to)
    {
        $result = [];
        $carbonFrom = Carbon::parse($from);
        $carbonTo = Carbon::parse($to);
        while ($carbonFrom->lte($carbonTo)) {
            $date = $carbonFrom->format('Y-m-d');
            $result[$date] = [
                'timestamp' => (int)$carbonFrom->getPreciseTimestamp(3),
                'value' => 0
            ];
            $carbonFrom->addDays(1);
        }

        return $result;
    }

    /**
     * Method normalizeText
     *
     * @param string $text
     *
     * @return string
     */
    public static function normalizeText(string $text)
    {
        return ucwords(strtolower(trim($text)));
    }

    /**
     * Method sendExceptionEmail
     *
     * @param Throwable $exception [explicite description]
     *
     * @return void
     */
    public static function sendExceptionEmail(Throwable $exception)
    {
        try {
            $content['message'] = $exception->getMessage();
            $content['file'] = $exception->getFile();
            $content['line'] = $exception->getLine();
            $content['trace'] = $exception->getTrace();

            $content['url'] = !empty(request()->fullUrl()) ? request()->fullUrl() : request()->url();
            $content['body'] = request()->all();
            $content['ip'] = request()->ip();

            if (in_array(config('app.env'), ['production', 'develop', /* 'local' */])) {
                $cc = ['admin_other@zeus.vision'];

                Mail::to('admin2@zeus.vision')
                        // ->cc($cc)
                        ->send(new ExceptionOccuredMail($content));
            }
        } catch (Throwable $exception) {
            Log::error($exception);
        }
    }

    /**
     * Method getMonthsByDateRange
     *
     * @param Carbon\Carbon $start [explicite description]
     * @param Carbon\Carbon $end [explicite description]
     * @param bool $withYear [explicite description]
     *
     * @return array
     */
    public static function getMonthsByDateRange(Carbon $start, Carbon $end, bool $withYear = false)
    {
        $interval = '1 day';
        $period   = CarbonPeriod::create($start, $interval, $end);

        $months = [];
        foreach ($period as $date) {
            $month = $withYear ? $date->format('Ym') : $date->month;
            if (in_array($month, $months) == false) {
                $months[] = $month;
            }
        }

        return $months;
    }

    /**
     * Method getIdentifiersString
     *
     * @param $collection $collection
     * @param string $identifier
     *
     * @return string
     */
    public static function getIdentifiersString($collection, string $identifier = 'id')
    {
        if (empty($collection)) {
            return '';
        }

        return $collection->pluck($identifier)->join(',');
    }

    /**
     * Method check_in_range
     *
     * @param string $date_start
     * @param string $date_end
     * @param string $date
     *
     * @return bool
     */
    public static function check_in_range(string $date_start, string $date_end, string $date)
    {
        $date_start = strtotime($date_start);
        $date_end = strtotime($date_end);
        $date = strtotime($date);

        return ($date >= $date_start && $date <= $date_end);
    }

    /**
     * Method stringIntersect
     *
     * @param string $a
     * @param string $b
     *
     * @return string
     */
    public static function stringIntersect(string $a, string $b)
    {
        $result = '';
        $len = strlen($a) > strlen($b) ? strlen($b) : strlen($a);
        for($i=0; $i<$len; $i++)
        {
            if(substr($a, $i, 1) == substr($b, $i, 1))
            {
                $result .= substr($a, $i, 1);
            } else
            {
                break;
            }
        }
        return $result;
    }

    /**
     * Method resetMaxAutoIncrementId
     *
     * @param string $table
     *
     * @return void
     */
    public static function resetMaxAutoIncrementId(string $table)
    {
        if (Schema::hasTable($table)) {
            $resetId = DB::table($table)->max('id')+1;
            DB::statement("ALTER TABLE $table AUTO_INCREMENT = $resetId");
        }
    }
}
