<?php

use Carbon\Carbon;

if (! function_exists('convertToLocalTimeStamp')) {
    function convertToLocalTimeStamp($date, $format = 'Y-m-d H:i:s')
    {
        return Carbon::make($date)
            ->setTimezone(config('app.timezone'))
            ->format($format);
    }
}
