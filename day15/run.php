<?php

declare(strict_types=1);

$input = file_exists('input.txt') ? file_get_contents('input.txt') : null;

$useExample = $input === null;

$input ??= <<<EXMAPLE
Sensor at x=2, y=18: closest beacon is at x=-2, y=15
Sensor at x=9, y=16: closest beacon is at x=10, y=16
Sensor at x=13, y=2: closest beacon is at x=15, y=3
Sensor at x=12, y=14: closest beacon is at x=10, y=16
Sensor at x=10, y=20: closest beacon is at x=10, y=16
Sensor at x=14, y=17: closest beacon is at x=10, y=16
Sensor at x=8, y=7: closest beacon is at x=2, y=10
Sensor at x=2, y=0: closest beacon is at x=2, y=10
Sensor at x=0, y=11: closest beacon is at x=2, y=10
Sensor at x=20, y=14: closest beacon is at x=25, y=17
Sensor at x=17, y=20: closest beacon is at x=21, y=22
Sensor at x=16, y=7: closest beacon is at x=15, y=3
Sensor at x=14, y=3: closest beacon is at x=15, y=3
Sensor at x=20, y=1: closest beacon is at x=15, y=3
EXMAPLE;

preg_match_all('/Sensor at x=(-?\d+), y=(-?\d+): closest beacon is at x=(-?\d+), y=(-?\d+)/m', $input, $matches, \PREG_SET_ORDER);
$input = array_map(static fn(array $positions): array => array_map('intval', array_slice($positions, 1)), $matches);

// Part 1

$targetY = $useExample ? 10 : 2000000;
$targetLine = [];
$ranges = [];
$beacons = [];

foreach ($input as [$sX, $sY, $bX, $bY]) {
    $dX = abs($bX - $sX);
    $dY = abs($bY - $sY);
    $bDist = $dX + $dY;
    [$minY, $maxY] = [$sY - $bDist, $sY + $bDist];
    if (!($targetY >= $minY && $targetY <= $maxY)) {
        continue;
    }

    if ($bY === $targetY) {
        $beacons[] = $bX;
    }
    $targetDist = $bDist - abs($targetY - $sY);
    if (!$targetDist) {
        continue;
    }
    $ranges[] = [$minX, $maxX] = [$sX - $targetDist, $sX + $targetDist];
}

// TODO: merge overlapping ranges and simply sum max-min. (still excluding beacon positions)
$line = [];
foreach ($ranges as [$min, $max]) {
    for ($i = $min; $i <= $max; $i++) {
        if (in_array($i, $beacons, true)) {
            continue;
        }
        $line[$i] ??= true;
    }
}

$result = count($line);

echo 'Part 1: ', $result, \PHP_EOL;


// Part 2

function mergeRanges(array $ranges, bool $mergeTouching = true): array
{
    $maxDiff = $mergeTouching ? 1 : 0;
    $mergedRanges = [array_shift($ranges)];
    while (true) {
        ksort($ranges);
        $foo = false;
        while (count($ranges)) {
            $merged = false;
            [$min, $max] = array_shift($ranges);
            foreach ($mergedRanges as $k => [$eMin, $eMax]) {
                if (($eMin - $max) > $maxDiff || ($min - $eMax) > $maxDiff) {
                    continue;
                }
                $mergedRanges[$k] = [min($min, $eMin), max($max, $eMax)];
                $merged = true;
                $foo = true;
                break;
            }

            if (!$merged) {
                $mergedRanges[] = [$min, $max];
            }
        }
        if ($foo) {
            $ranges = $mergedRanges;
            $mergedRanges = [array_shift($ranges)];
            continue;
        }

        return $mergedRanges;
    }
}

$targetMin = 0;
$targetMax = $useExample ? 20 : 4000000;
$xMultiplier = 4000000;

$targetMinY = $targetMin;
$targetMaxY = $targetMax;
$ranges = [];
foreach ($input as $n => [$sX, $sY, $bX, $bY]) {
    $dX = abs($bX - $sX);
    $dY = abs($bY - $sY);
    $bDist = $dX + $dY;
    [$minY, $maxY] = [max($sY - $bDist, $targetMinY), min($sY + $bDist, $targetMaxY)];

    for ($y = $minY; $y <= $maxY; $y++) {
        $targetDist = $bDist - abs($y - $sY);
        $range = [$sX - $targetDist, $sX + $targetDist];
        $ranges[$y][] = $range;
    }
}

$result = null;
foreach ($ranges as $y => $xRanges) {
    $xRanges = mergeRanges($xRanges);
    if (count($xRanges) === 1 && $xRanges[0][0] <= $targetMin && $xRanges[0][1] >= $targetMax) {
        continue;
    }
    if (count($xRanges) === 2 && $xRanges[0][1] + 2 === $xRanges[1][0]) {
        $x = $xRanges[0][1] + 1;
        $result = ($x * $xMultiplier) + $y;
        break;
    }
}

if ($result === null) {
    throw new \RuntimeException('Failed to find beacon! It might be at the edge of the window.');
}

echo 'Part 2: tuning frequency: ', $result, \PHP_EOL;
