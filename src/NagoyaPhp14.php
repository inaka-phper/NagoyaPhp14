<?php

declare(strict_types=1);

namespace InakaPhper\NagoyaPhp14;

class NagoyaPhp14
{
    private $parser;

    public function run(string $input): int
    {
        $this->parser = new Parser($input);

        return $this->calc();
    }

    private function calc()
    {
//        print_r($this->parser);
        return $this->parser->diff();
    }
}

class Parser
{
    private $start;
    private $end;
    private $base;

    public function __construct($input)
    {
        $args = explode(",", $input);
        $this->base = (int)$args[2];

        $this->start = new Converter((int)$args[0], $this->base);
        $this->end = new Converter((int)$args[1], $this->base);

    }

    public function diff()
    {
        return (new NormalDiff($this))->diff();
        if (strlen((string)$this->start->number()) === 1) {
//            return (new NormalDiff($this))->diff();
        }
        if ((strlen((string)$this->start()->convert()) + 2) <= strlen((string) $this->end()->convert())) {
//            echo "over";
        }

//        echo strlen((string)$this->start->number());
        if ($this->start->isEqually() && $this->end->isEqually()) {
//            echo 'EVEN';
        }
        if (!$this->start->isEqually() && !$this->end->isEqually()) {
//            echo 'ODD';
//            return (new NormalDiff($this))->diff();
        }
        if ($this->start->isEqually() && !$this->end->isEqually()) {
//            echo '-';
//            return (new NormalDiff($this))->diff();
        }
        if (!$this->start->isEqually() && $this->end->isEqually()) {
//            return (new NormalDiff($this))->diff();
//            echo '=';
//            return (new NormalDiff($this))->diff();
        }

//        echo $this->start()->convert() . ':' . $this->end()->convert() . '=' . strlen((string)$this->start()->convert()) . ':' . strlen((string) $this->end()->convert()) . PHP_EOL;
        return (new SeparateDiff($this))->diff();

    }

    public function start()
    {
        return $this->start;
    }

    public function end()
    {
        return $this->end;
    }

    public function isAfter()
    {
        return $this->decode($this->start->palindrome()) >= $this->start->number();
    }

    public function isIn()
    {
        return $this->decode($this->end->palindrome()) < $this->end->number();
    }

    public function getBase(): int
    {
        return $this->base;
    }

    public function decode($tobase) : int
    {
        return (int) base_convert($tobase, $this->base, 10);
    }
}

interface Diff
{
    public function diff();
}

class SeparateDiff implements Diff
{

    private $parser;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    public function diff()
    {
        $result = 0;
        $start = $this->parser->decode($this->parser->start()->separate()) - 1;
        $end = $this->parser->decode($this->parser->end()->separate());
        $adjust = ($this->parser->isAfter() ? 0 : 1) + ($this->parser->isIn() ? 0 : 1);

        $diff = $this->parser->end()->length() - $this->parser->start()->length() - (strlen($this->parser->start()->separate()) % 2);

//        echo PHP_EOL . 'diff:' . $diff . PHP_EOL;
//        echo strlen($this->parser->start()->separate()) . PHP_EOL;
        $length = strlen($this->parser->start()->separate());
        $count = strlen($this->parser->start()->convert()) % 2;


        if ($diff > 0) {
//            echo 'run!' . PHP_EOL;
//            echo 'count:' . $count . PHP_EOL;
//            echo '---------' . PHP_EOL;
            for ($i = 0; $i < $diff; $i++) {
                if ($count % 2 === 0) {
                    $count++;
                    continue;
                }

                $max = base_convert($this->parser->getBase() - 1, 10, $this->parser->getBase());
                $up = str_repeat($max, $length);
                $border = base_convert($up, $this->parser->getBase(), 10) + 1;

                $result += $border - $start;

//                echo 'b-s:' . $border . '->' . $start . PHP_EOL;
//                echo 'start:' . $start . PHP_EOL;
//                echo 'result:' . $result . PHP_EOL;
//                echo 'count:' . $count . PHP_EOL;
//                echo 'length:' . (int) str_pad('1', $length, '0', STR_PAD_RIGHT) . PHP_EOL;
//                echo '---------' . PHP_EOL;
                $start = base_convert((int) str_pad('1', $length, '0', STR_PAD_RIGHT), $this->parser->getBase(), 10);

                $count++;
                $length++;
            }
        }

//        echo $this->parser->getBase(). ' ' . $up . ' ' . $border .  PHP_EOL;
//        print_r([
//            $this->parser->start()->separate() => [$start => $this->parser->start()->convert()],
//            $this->parser->end()->separate() => [$end => $this->parser->end()->convert()],
//            'adjuster' => $adjust
//        ]);

        return $result + $end - $start - $adjust;
    }
}

class NormalDiff implements Diff
{
    private $parser;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    public function diff()
    {
        $start = $this->parser->start()->number();
        $end = $this->parser->end()->number();
        $tobase = $this->parser->getBase();

        echo PHP_EOL;
        $visibleCounter = 0;
        for ($i = $start; $i < $end; $i++) {
            $value = base_convert($i, 10, $tobase);

            if ($value === strrev($value)) {
                echo substr($value, 0, (int) ceil(strlen($value) / 2)) . ' ' . $value . ' ' . base_convert($value, $tobase, 10) . ' ' . base_convert(substr($value, 0, (int) ceil(strlen($value) / 2)), $tobase, 10) . PHP_EOL;

//                echo $visibleCounter .':'.$value.PHP_EOL;
                $visibleCounter++;
            }
        }
        echo 'total:' . $visibleCounter . PHP_EOL;
        echo (new SeparateDiff($this->parser))->diff();

        // implement me
        return $visibleCounter;
    }
}

class Converter
{
    private $number;
    private $convert;
    private $base;

    public function __construct(int $number, $base)
    {
        $this->number = $number;
        $this->base = $base;
        $this->convert = base_convert($this->number, 10, $this->base);
    }

    public function number() : int
    {
        return $this->number;
    }

    public function convert()
    {
        return $this->convert;
    }

    public function separate()
    {
        return substr($this->convert, 0, (int) ceil(strlen($this->convert) / 2));
    }

    public function length()
    {
        return strlen($this->convert);
    }

    public function palindrome()
    {
        $separate = $this->separate();
        $reverse = strrev($separate);

        return $this->isEqually() ? $separate . $reverse : $separate . substr($reverse, 1);
    }

    public function isEqually()
    {
        return strlen($this->convert) % 2 === 0;
    }
}

