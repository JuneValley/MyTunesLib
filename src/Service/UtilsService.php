<?php

namespace App\Service;

class UtilsService {
    /**
     * Returns a song duration formatted in minutes:seconds
     * @param int $durationInSeconds the song duration in seconds
     * @return string the formatted duration
     */
    public function formatDuration(int $durationInSeconds): string {
        $minutes = intdiv($durationInSeconds, 60);
        $seconds = $durationInSeconds - $minutes*60;
        
        if($seconds < 10){
            return strval($minutes) . ':0' . strval($seconds);
        } else {
            return strval($minutes) . ':' . strval($seconds);
        }
    }
}