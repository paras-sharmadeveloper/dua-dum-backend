<?php

namespace App\Services\HelperServices;

use Illuminate\Http\Request;

class DeviceIdentificationService
{
    public function isMobileDevice(Request $request)
    {
        $userAgent = $request->header('User-Agent');
        $isMobile = preg_match("/(android|blackberry|iphone|ipod|mobile|palm|phone|windows\s+ce)/i", $userAgent);
        return $isMobile;
    }
}