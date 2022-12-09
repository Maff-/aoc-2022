<?php

declare(strict_types=1);

$input = file_exists('input.txt') ? file_get_contents('input.txt') : null;

$input ??= <<<EXMAPLE
R 4
U 4
L 3
D 1
R 4
D 1
L 5
R 2
EXMAPLE;

$input = explode("\n", trim($input, "\n"));
$input = array_map(static fn ($line) => [$line[0], (int)substr($line, 2)], $input);

// [x,y]
$directions = [
    'U' => [0, -1], // up
    'D' => [0, +1], // down
    'L' => [-1, 0], // left
    'R' => [+1, 0], // right
];

// Part 1

$trail = [];
$tailPos = $headPos = [0, 0];
foreach ($input as [$dir, $times]) {
    [$dX, $dY] = $directions[$dir];
    for ($n = 0; $n < $times; $n++) {
        $headPos = [$headPos[0] + $dY, $headPos[1] + $dX];
        $distY = $headPos[0] - $tailPos[0];
        $distX = $headPos[1] - $tailPos[1];
        if ($distY < -1 || $distY > 1 || $distX < -1 || $distX > 1) {
            $tailPos = [$tailPos[0] + max(-1, min(+1, $distY)), $tailPos[1] + max(-1, min(+1, $distX))];
        }
        $trail[$tailPos[0]][$tailPos[1]] = 1;
    }
}

$result = array_sum(array_map('array_sum', $trail));

echo 'Part 1: ', $result, \PHP_EOL;
