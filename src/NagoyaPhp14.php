<?php

declare(strict_types=1);

namespace InakaPhper\NagoyaPhp14;

class NagoyaPhp14
{
    public function run(string $input): int
    {
        $args = explode(",", $input);
        $start = (int)$args[0];
        $end = (int)$args[1];
        $tobase = (int)$args[2];


        $visibleCounter = 0;
        for ($i = $start; $i < $end; $i++) {
            $number = base_convert($i, 10, $tobase);
            if ($number === strrev($number)) {
                echo $number . PHP_EOL;
                $visibleCounter++;
            }
        }

        return $visibleCounter;
    }
}
