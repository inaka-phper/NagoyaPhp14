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
            if ($this->isPalindrome(base_convert($i, 10, $tobase))) {
                $visibleCounter++;
            }
        }

        // implement me
        return $visibleCounter;
    }

    public function isPalindrome($value)
    {
        $reverse = implode("", array_reverse(str_split($value)));

        return $value === $reverse;
    }
}
