<?php

declare(strict_types=1);

$input = file_exists('input.txt') ? file_get_contents('input.txt') : null;

$input ??= <<<EXMAPLE
30373
25512
65332
33549
35390
EXMAPLE;

$input = explode("\n", trim($input, "\n"));
$input = array_map(static fn ($line) => array_map('intval', str_split($line)), $input);
$height = count($input);
$width = $height ? count($input[0]) : 0;

// [x,y]
$directions = [
    'u' => [0, -1], // up
    'd' => [0, +1], // down
    'l' => [-1, 0], // left
    'r' => [+1, 0], // right
];

// Part 1

$visibleCount = 0;
for ($y = 0; $y < $height; $y++) {
    for ($x = 0; $x < $width; $x++) {
        $pos = [$x, $y];
        $size = $input[$y][$x];
        foreach ($directions as $dir => [$dX, $dY]) {
            $visible = true;
            for (
                $cX = $x + $dX, $cY = $y + $dY;
                $cX >= 0 && $cX < $width && $cY >= 0 && $cY < $height;
                $cX += $dX, $cY += $dY
            ) {
                $check = $input[$cY][$cX];
                if ($check >= $size) {
                    $visible = false;
                    break;
                }
            }
            if ($visible) {
                $visibleCount++;
                break;
            }
        }
    }
}

echo 'Part 1: ', $visibleCount, \PHP_EOL;


// Part 2

$maxScenicScore = 0;
// iterate every position, excluding edges
for ($y = 1; $y < ($height - 1); $y++) {
    for ($x = 1; $x < ($width - 1); $x++) {
        $pos = [$x, $y];
        $size = $input[$y][$x];
        $score = null;
        foreach ($directions as $dir => [$dX, $dY]) {
            $visibleCount = 0;
            for (
                $cX = $x + $dX, $cY = $y + $dY;
                $cX >= 0 && $cX < $width && $cY >= 0 && $cY < $height;
                $cX += $dX, $cY += $dY
            ) {
                $check = $input[$cY][$cX];
                $visibleCount++;
                if ($check >= $size) {
                    break;
                }
            }
            if ($visibleCount === 0) {
                continue 2; // skip this tree, scenic score of 0 won't be good enough
            }
            $score ??= 1;
            $score *= $visibleCount;
        }
        $maxScenicScore = max($maxScenicScore, $score);
    }
}

echo 'Part 2: ', $maxScenicScore, \PHP_EOL;
