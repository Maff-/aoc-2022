<?php

declare(strict_types=1);

$input = file_exists('input.txt') ? file_get_contents('input.txt') : null;

$input ??= <<<EXMAPLE
498,4 -> 498,6 -> 496,6
503,4 -> 502,4 -> 502,9 -> 494,9
EXMAPLE;

$input = explode("\n", rtrim($input, "\n"));
$input = array_map(static fn (string $line) => array_map(static fn(string $coords) => array_map('intval', explode(',', $coords)), explode(' -> ', $line)), $input);

const X = 0;
const Y = 1;
const ROCK = '#';
const AIR = '.';

// Part 1

$moves = [
    [-0, 1], // down
    [-1, 1], // down-left
    [+1, 1], // down-right
];

$map = [];
$minX = $maxX = $input[0][0][X];
$minY = $maxY = $input[0][0][Y];
foreach ($input as $coords) {
    [$xA, $yA] = array_shift($coords);
    $minX = min($minX, $xA);
    $minY = min($minY, $yA);
    $maxX = max($maxX, $xA);
    $maxY = max($maxY, $yA);
    foreach ($coords as $coord) {
        [$xB, $yB] = $coord;
        $minX = min($minX, $xB);
        $minY = min($minY, $yB);
        $maxX = max($maxX, $xB);
        $maxY = max($maxY, $yB);
        [$xA, $xB] = $xA < $xB ? [$xA, $xB] : [$xB, $xA];
        [$yA, $yB] = $yA < $yB ? [$yA, $yB] : [$yB, $yA];
        for ($x = $xA; $x <= $xB; $x++) {
            for ($y = $yA; $y <= $yB; $y++) {
                $map[$y][$x] = ROCK;
            }
        }
        [$xA, $yA] = $coord;
    }
}

$sandSource = [500, 0];
//$map[$sandSource[Y]][$sandSource[X]] = '+';

$width = $maxX - $minX + 1;
$height = $maxY - $minY + 1;

$units = 0;

while (true) {
    $sandPos = $sandSource;
    while (true) {
        $moved = false;
        foreach ($moves as [$dX, $dY]) {
            $x = $sandPos[X] + $dX;
            $y = $sandPos[Y] + $dY;
            if ($y > $maxY) {
                break 3;
            }
            $foo = $map[$y][$x] ?? AIR;
            if ($foo === AIR) {
                $sandPos = [$x, $y];
                $moved = true;
                break;
            }
        }
        if (!$moved) {
            $units++;
            $map[$sandPos[Y]][$sandPos[X]] = 'O';
            break;
        }
    }
}

echo 'Part 1: units of sand rested: ', $units, \PHP_EOL;
