<?php

namespace App\Traits;

use App\Models\Point;
use App\Models\PointLedger;
use Carbon\Carbon;

trait AttendancePointTrait
{
    /**
     * Calculate points based on time and status.
     *
     * @param string|null $time In format 'H:i:s'
     * @param string $status Result status (hadir, telat, alpha, etc)
     * @param bool $isUsingVoucher
     * @return array Contains 'points' (int) and 'descriptions' (array)
     */
    public function calculateAttendancePoints($time, $status, $isUsingVoucher = false)
    {
        $pointsEarned = 0;
        $matchingRulesDesc = [];

        // Ambil semua aturan point yang aktif
        $activeRules = Point::where('status', 1)->get();

        foreach ($activeRules as $rule) {
            $isMatch = false;

            // 1. Jika aturan memiliki kondisi WAKTU (seperti Hadir/Telat)
            if (in_array($rule->condition_operator, ['<', '>', 'BETWEEN'])) {
                if ($time) {
                    $checkInTimeStr = Carbon::parse($time)->format('H:i:s');
                    
                    switch ($rule->condition_operator) {
                        case '<':
                            $ruleTimeStr = Carbon::parse($rule->condition_value)->format('H:i:s');
                            if ($checkInTimeStr < $ruleTimeStr) $isMatch = true;
                            break;
                        case '>':
                            $ruleTimeStr = Carbon::parse($rule->condition_value)->format('H:i:s');
                            if ($checkInTimeStr > $ruleTimeStr) $isMatch = true;
                            break;
                        case 'BETWEEN':
                            $separator = str_contains($rule->condition_value, '-') ? '-' : ',';
                            $parts = explode($separator, $rule->condition_value);
                            if (count($parts) >= 2) {
                                $start = Carbon::parse(trim($parts[0]))->format('H:i:s');
                                $end = Carbon::parse(trim($parts[1]))->format('H:i:s');
                                if ($checkInTimeStr >= $start && $checkInTimeStr <= $end) {
                                    $isMatch = true;
                                }
                            }
                            break;
                    }
                }
            } 
            // 2. Jika aturan TIDAK memiliki kondisi waktu (seperti Alpha, Izin, Sakit)
            else {
                if (strtolower($rule->name) === strtolower($status)) {
                    $isMatch = true;
                }
            }

            if ($isMatch) {
                // JIKA GURU PAKAI VOUCHER & STATUS JADI HADIR: Abaikan Penalti (Poin Negatif)
                if ($isUsingVoucher && $status === 'hadir' && $rule->point_modifier < 0) {
                    $matchingRulesDesc[] = $rule->name . ' (Diabaikan via Voucher)';
                } else {
                    $pointsEarned += $rule->point_modifier;
                    $matchingRulesDesc[] = $rule->name;
                }
            }
        }

        return [
            'points'       => $pointsEarned,
            'descriptions' => $matchingRulesDesc
        ];
    }

    /**
     * Calculate minutes late based on the 'hadir' rule threshold.
     *
     * @param string|null $time In format 'H:i:s'
     * @return int
     */
    public function calculateLateMinutes($time)
    {
        if (!$time) return 0;

        $checkInTime = Carbon::parse($time);
        $thresholdTimeStr = '00:00';

        // Cari aturan "hadir" untuk mengambil jam selesainya (Batas Akhir Tepat Waktu)
        $hadirRule = Point::where('name', 'hadir')->where('status', 1)->first();
        
        if ($hadirRule && $hadirRule->condition_operator === 'BETWEEN') {
            $separator = str_contains($hadirRule->condition_value, '-') ? '-' : ',';
            $parts = explode($separator, $hadirRule->condition_value);
            if (count($parts) >= 2) {
                $thresholdTimeStr = trim($parts[1]);
            }
        } else {
            // Fallback ke aturan telat jika hadir tidak ditemukan
            $telatRule = Point::where('name', 'telat')->where('status', 1)->first();
            if ($telatRule) {
                $thresholdTimeStr = $telatRule->condition_value;
            }
        }

        $lateLimit = Carbon::parse($time)->setTimeFromTimeString($thresholdTimeStr);
        
        if ($checkInTime->gt($lateLimit)) {
            $diffSeconds = $checkInTime->diffInSeconds($lateLimit);
            return (int) ceil($diffSeconds / 60);
        }

        return 0;
    }
}
