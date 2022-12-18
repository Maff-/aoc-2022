<?php

declare(strict_types=1);

$input = file_exists('input.txt') ? file_get_contents('input.txt') : null;

$input ??= <<<EXMAPLE
2,2,2
1,2,2
3,2,2
2,1,2
2,3,2
2,2,1
2,2,3
2,2,4
2,2,6
1,2,5
3,2,5
2,1,5
2,3,5
EXMAPLE;

$input = array_map(static fn ($line) => array_map('intval', explode(',', $line)), explode("\n", trim($input)));

const DIRECTIONS = [
    [+1, 0, 0],
    [-1, 0, 0],
    [0, +1, 0],
    [0, -1, 0],
    [0, 0, +1],
    [0, 0, -1],
];

$grid = [];
foreach ($input as [$x, $y, $z]) {
    $grid[$z][$y][$x] = true;
}

$surfaceArea = 0;
foreach ($input as [$x, $y, $z]) {
    $neighbours = 0;
    foreach (DIRECTIONS as [$dX, $dY, $dZ]) {
        $neighbour = $grid[$z + $dZ][$y + $dY][$x + $dX] ?? false;
        $neighbours += $neighbour;
    }
    $surfaceArea += (6 - $neighbours);
}

$result = $surfaceArea;

echo 'Part 1: surface area: ', $result, \PHP_EOL;
