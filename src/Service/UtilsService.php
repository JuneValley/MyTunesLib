<?php

namespace App\Service;

class UtilsService {
    /**
     * Returns a song duration formatted in minutes and seconds.
     * @param int $durationInSeconds The song duration in seconds only.
     * @return string The formatted duration.
     */
    public function formatDurationToMinutesSeconds(int $durationInSeconds, bool $isFromController): string
    {
        $minutes = intdiv($durationInSeconds, 60);
        $seconds = $durationInSeconds - $minutes*60;

        if($isFromController){
            return strval($minutes) . ',' . strval($seconds);
        }
        
        if($seconds < 10)
        {
            return strval($minutes) . ':0' . strval($seconds);
        }

        return strval($minutes) . ':' . strval($seconds);
    }

    /**
     * Returns a song duration in seconds.
     * @param int $durationInMinutes The song duration in minutes.
     * @param int $durationInSeconds The song duration in seconds.
     * @return int The duration in seconds.
     */
    public function formatDurationToSeconds(int $durationInMinutes, int $durationInSeconds): string
    {
        return $durationInMinutes*60 + $durationInSeconds;
    }
}