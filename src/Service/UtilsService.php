<?php

namespace App\Service;

class UtilsService {
    /**
     * Returns a song duration formatted in minutes:seconds
     * @param int $durationInSeconds the song duration in seconds
     * @return string the formatted duration
     */
    public function formatDurationToMinutesSeconds(int $durationInSeconds): string
    {
        $minutes = intdiv($durationInSeconds, 60);
        $seconds = $durationInSeconds - $minutes*60;
        
        if($seconds < 10)
        {
            return strval($minutes) . ':0' . strval($seconds);
        } else {
            return strval($minutes) . ':' . strval($seconds);
        }
    }

    /**
     * Returns a song duration in seconds
     * @param int $durationInMinutes the song duration in minutes
     * @param int $durationInSeconds the song duration in seconds
     * @return int the duration in seconds
     */
    public function formatDurationToSeconds(int $durationInMinutes, int $durationInSeconds): string
    {
        return $durationInMinutes*60 + $durationInSeconds;
    }
}