<?php

if (!function_exists('detectCountryFromPhone')) {
    function detectCountryFromPhone($phone)
    {
        if (!$phone) return null;

        // Example mappings
        if (str_starts_with($phone, '+233')) return 'GH'; // Ghana
        if (str_starts_with($phone, '+234')) return 'NG'; // Nigeria
        if (str_starts_with($phone, '+44')) return 'GB'; // UK
        if (str_starts_with($phone, '+1')) return 'US'; // USA

        return null;
    }
}

if (!function_exists('detectCountryFromIp')) {
    function detectCountryFromIp($ip)
    {
        try {
            // requires "torann/geoip" package
            $geo = geoip($ip);
            return $geo->iso_code; // e.g. "GH"
        } catch (\Exception $e) {
            return null;
        }
    }
}
