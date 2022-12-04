<?php

declare(strict_types=1);

$input = file_exists('input.txt') ? file_get_contents('input.txt') : null;

$input ??= <<<EXMAPLE
2-4,6-8
2-3,4-5
5-7,7-9
2-8,3-7
6-6,4-6
2-6,4-8
EXMAPLE;

$input = explode("\n", trim($input));
$input = array_map(static fn($line) => array_map('intval', preg_split('/[,-]/', $line)), $input);

// Part 1

$contained = 0;
foreach ($input as [$startA, $endA, $startB, $endB]) {
    if (($startB <= $startA && $endB >= $endA) || ($startA <= $startB && $endA >= $endB)) {
        $contained++;
    }
}

echo 'Part 1; Assignment pairs where one range fully contain the other: ', $contained, \PHP_EOL;
