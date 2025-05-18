<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class GeoLocationService
{
    public function getUserTimezoneByIp($ip = null)
    {
        $ip = $ip ?? request()->ip();

        return Cache::remember("ip-timezone-{$ip}", 3600, function () use ($ip) {
            $response = @file_get_contents("http://ip-api.com/json/{$ip}?fields=status,message,timezone");

            if ($response) {
                $data = json_decode($response);
                if ($data->status === 'success') {
                    return $data->timezone; // e.g., "Africa/Lagos"
                }
            }

            return config('app.timezone'); // fallback to default
        });
    }
}
