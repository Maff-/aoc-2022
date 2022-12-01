<?php

declare(strict_types=1);

$input = file_exists('input.txt') ? file_get_contents('input.txt') : null;

$input ??= <<<EXMAPLE
1000
2000
3000

4000

5000
6000

7000
8000
9000

10000
EXMAPLE;

$input = explode("\n\n", trim($input));
$input = array_map(static fn($chunk) => array_map('intval', explode("\n", $chunk)), $input);

// Part 1

$max = null;

foreach ($input as $calories) {
    $sum = array_sum($calories);
    $max = $sum > $max ? $sum : $max;
}

echo $max, \PHP_EOL;


// Part 2

$sums = array_map('array_sum', $input);
rsort($sums);
$top3sum = array_sum(array_slice($sums, 0, 3));

echo $top3sum, \PHP_EOL;
