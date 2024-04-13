<?php

namespace App\Helper;

use Carbon\Carbon;

class DateTime
{
    /**
     * Format date and time according to Indian Standard Time (IST).
     *
     * @return string The formatted date and time.
     */
    public static function formatDateTime()
    {
        $now = Carbon::now();
        $now->setTimezone('Asia/Kolkata');
        return $now->format('d-m-Y H:i:s');
    }
}
