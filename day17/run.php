<?php

declare(strict_types=1);

$input = file_exists('input.txt') ? file_get_contents('input.txt') : null;

$input ??= <<<EXMAPLE
>>><<><>><<<>><>>><<<>>><<<><<<>><>><<>>
EXMAPLE;

$input = str_split(trim($input));

$jets = $input;
$jetCount = count($jets);

$rocks = [
    [
        0b0011110,
    ],
    [
        0b0001000,
        0b0011100,
        0b0001000,
    ],
    [
        0b0000100,
        0b0000100,
        0b0011100,
    ],
    [
        0b0010000,
        0b0010000,
        0b0010000,
        0b0010000,
    ],
    [
        0b0011000,
        0b0011000,
    ],
];
// Flip Y order, so we can use foreach to iterate over each line
$rocks = array_map('array_reverse', $rocks);
$rockCount = count($rocks);

function printMap(array $lines, ?int $height = null): void
{
    $height ??= max(array_keys($lines)) + 1;
    for ($y = $height - 1; $y >= 0; $y--) {
        echo '|';
        for ($x = 1 << 6; $x > 0; $x >>= 1) {
            echo (($lines[$y] ?? 0) & $x) ? '#' : '.';
        }
        echo '|', \PHP_EOL;
    }
    echo '+-------+', \PHP_EOL;
}

// Part 1

$width = 7;
$min = 1 << 0;
$max = 1 << ($width - 1);
$jetIndex = 0;
$rockIndex = 0;
$height = 0; // start width floor
$lines = [];

for ($i = 0; $i < 2022; $i++) {
    $rock = $rocks[$rockIndex];
    $rockBottom = $height + 3;
    $stopped = false;
    while (!$stopped) {
        // Jet push
        $jet = $jets[$jetIndex];
        $failedMove = false;
        $prevRock = $rock;
        if ($jet === '<') {
            foreach ($rock as $n => $rockLine) {
                if ($rockLine & $max) {
                    // against left wall
                    $failedMove = true;
                    break;
                }
                $rock[$n] <<= 1;
            }
        } else {
            foreach ($rock as $n => $rockLine) {
                if ($rockLine & $min) {
                    // against right wall
                    $failedMove = true;
                    break;
                }
                $rock[$n] >>= 1;
            }
        }
        // check with other rocks
        foreach ($rock as $n => $rockLine) {
            if ((($lines[$rockBottom + $n] ?? 0) & $rockLine) !== 0) {
                $failedMove = true;
                break;
            }
        }
        if ($failedMove) {
            $rock = $prevRock;
        }

        // Fall
        $rockBottom--;

        $failedFall = $rockBottom < 0;
        if (!$failedFall) {
            foreach ($rock as $n => $rockLine) {
                if ((($lines[$rockBottom + $n] ?? 0) & $rockLine) !== 0) {
                    $failedFall = true;
                    break;
                }
            }
        }

        if ($failedFall) {
            $rockBottom++;
            foreach ($rock as $n => $rockLine) {
                $lines[$rockBottom + $n] ??= 0;
                $lines[$rockBottom + $n] |= $rockLine;
            }
            $height = max($height, $rockBottom + count($rock));
            $stopped = true;
        }

        $jetIndex = ($jetIndex + 1) % $jetCount;
    }

    $rockIndex = ($rockIndex + 1) % $rockCount;
}

//echo ($i+1), ' rocks dropped:', \PHP_EOL;
//printMap($lines);
//echo \PHP_EOL;

$result = $height;

echo 'Part 1: ', $result, \PHP_EOL;
