<?php

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

if (! function_exists('getPerPageQueryParameter')) {

    /**
     * Get the per page query parameter.
     */
    function getPerPageQueryParameter($perPageParameter, int $min = 1, int $max = 50): int
    {
        return is_numeric($perPageParameter)
        && $perPageParameter <= $max
        && $perPageParameter >= $min ? (int) request('perPage') : 10;
    }
}

if (! function_exists('apiResponse')) {
    function apiResponse($data, int $code = 200): JsonResponse
    {
        return response()->json([
            'data' => $data,
        ], $code);
    }
}

if (! function_exists('convertToLocalTimeStamp')) {
    function convertToLocalTimeStamp($date, $format = 'Y-m-d H:i:s')
    {
        return Carbon::make($date)
            ->setTimezone(config('app.timezone'))
            ->format($format);
    }
}
