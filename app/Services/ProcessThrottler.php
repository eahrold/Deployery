<?php

namespace App\Services;


class ProcessThrottler  {
    private $throttle = 0;

    public function __construct(int $seconds)
    {
        $this->throttle = $seconds;
    }

    public function exec(\Closure $func) {
        $time_start = microtime(true);
        $response = $func();
        $time_end = microtime(true);
        $sleep_for = (($time_start + $this->throttle) - $time_end);

        logger("Throttle Diff {$sleep_for} seconds");
        if ($sleep_for > 0) {
            $ms = (int)round($sleep_for*1000000);
            logger("Going To Sleep Now for {$ms} microseconds");
            usleep($ms);
        }

        return $response;
    }
}