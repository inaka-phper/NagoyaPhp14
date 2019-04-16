<?php

declare(strict_types=1);

namespace InakaPhper\NagoyaPhp14;

class NagoyaPhp14
{
    private $parser;

    public function run(string $input): int
    {
        $this->parser = new Parser($input);

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
        $start = $this->parser->start()->separateToTen() - 1;
        $end = $this->parser->end()->separateToTen();

        $adjust = ($this->parser->isAfter() ? 0 : 1) + ($this->parser->isIn() ? 0 : 1);

        $diff = $this->parser->end()->length() - $this->parser->start()->length();

        $length = strlen($this->parser->start()->separate());
        $count = strlen($this->parser->start()->convert()) % 2;


        if ($diff > 0) {
            for ($i = 0; $i < $diff; $i++) {
                if ($count % 2 === 0) {
                    if ($i === 0) {
                        $length++;
                    }
                    $count++;
                    continue;
                }

                $max = base_convert($this->parser->getBase() - 1, 10, $this->parser->getBase());
                $up = str_repeat($max, $length);
                $border = base_convert($up, $this->parser->getBase(), 10) + 1;

                $result += $border - $start;

                $start = base_convert((int) str_pad('1', $length, '0', STR_PAD_RIGHT), $this->parser->getBase(), 10);

                $count++;
                $length++;
            }
        }

        return $result + $end - $start - $adjust;
    }
}

class Converter
{
    private $number;
    private $convert;
    private $base;
    private $separate;
    private $separateToTen;

    public function __construct(int $number, $base)
    {
        $this->number = $number;
        $this->base = $base;
        $this->convert = base_convert($this->number, 10, $this->base);
        $this->separate = substr($this->convert, 0, (int) ceil(strlen($this->convert) / 2));
        $this->separateToTen = base_convert($this->separate, $this->base, 10);
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
        return $this->separate;
    }

    public function separateToTen()
    {
        return $this->separateToTen;
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

