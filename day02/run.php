<?php

declare(strict_types=1);

$input = file_exists('input.txt') ? file_get_contents('input.txt') : null;

$input ??= <<<EXMAPLE
A Y
B X
C Z
EXMAPLE;

$input = explode("\n", trim($input));
$input = array_map(static fn($chunk) => explode(' ', $chunk), $input);

// Part 1

$offsetMove = ord('A') - 1;
$offsetResponse = ord('X') - 1;
$label = [1 => 'R', 'P', 'S'];

// Wins R>S, S>P, P>R

$totalScore = 0;
foreach ($input as [$m, $r]) {
    $m = ord($m) - $offsetMove;
    $r = ord($r) - $offsetResponse;

    $mL = $label[$m];
    $rL = $label[$r];

    if ($m === $r) {
        // draw
        $score = $r + 3;
    } elseif (($rL === 'R' && $mL === 'S') || ($rL === 'S' && $mL === 'P') || ($rL === 'P' && $mL === 'R')) {
        // win
        $score = $r + 6;
    } else {
        // lose
        $score = $r;
    }
    $totalScore += $score;
}

echo 'Total score part 1: ', $totalScore, \PHP_EOL;

// Part 2

// Wins R(1)>S(3), S(3)>P(2), P(2)>R(1)
$win = [1 => 2, 2 => 3, 3 => 1];
$lose = array_flip($win);

$totalScore = 0;
foreach ($input as [$m, $r]) {
    $m = ord($m) - $offsetMove;
    $score = match ($r) {
        // lose
        'X' => $lose[$m] + 0,
        // draw
        'Y' => $m + 3,
        // win
        'Z' => $win[$m] + 6,
    };
    $totalScore += $score;
}

echo 'Total score part 2: ', $totalScore, \PHP_EOL;
