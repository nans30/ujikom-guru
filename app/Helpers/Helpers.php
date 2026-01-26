<?php

namespace App\Helpers;

use App\Models\Attachment;
use App\Models\Country;
use App\Models\Faq;
use App\Models\LandingPage;
use App\Models\Setting;
use App\Models\User;

class Helpers
{
    public static function isUserLogin()
    {
        return auth()?->check();
    }

    public static function getCurrentUserId()
    {
        if (self::isUserLogin()) {
            return auth()?->user()?->id;
        }
    }

    public static function getMedia($id)
    {
        return Attachment::find($id);
    }

    public static function getCountryCode()
    {
        return Country::get(["calling_code", "id", "iso_3166_2", 'flag'])->unique('calling_code');
    }

    public static function getUser()
    {
        $user = User::with('roles')->where('system_reserve', '!=', 1)->latest()->take(5)->get();
        return $user;
    }
    public static function getSettings()
    {
        return Setting::pluck('values')?->first();
    }

    public static function getSettingPageContent()
    {
        return Setting::first()->values;
    }

    public static function getStatus()
    {
        return [
            0 => 'Inactive',
            1 => 'Active',
        ];
    }

    public static function getMaxUploadFileSize(): int
    {
        $uploadMax = self::convertPHPSizeToBytes(ini_get('upload_max_filesize'));
        $postMax   = self::convertPHPSizeToBytes(ini_get('post_max_size'));

        return min($uploadMax, $postMax);
    }

    private static function convertPHPSizeToBytes(string $size): int
    {
        $unit = strtolower(substr($size, -1));
        $bytes = (int) $size;

        switch ($unit) {
            case 'g':
                $bytes *= 1024;
            case 'm':
                $bytes *= 1024;
            case 'k':
                $bytes *= 1024;
        }

        return $bytes;
    }
}
