<?php

declare(strict_types=1);

$input = file_exists('input.txt') ? file_get_contents('input.txt') : null;
//$input = null;

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
//    echo "$m $r -> ";
    $m = ord($m) - $offsetMove;
    $r = ord($r) - $offsetResponse;
//    echo "$m $r -> ";
//    echo "{$label[$m]} {$label[$r]} -> ";

    $mL = $label[$m];
    $rL = $label[$r];

    if ($m === $r) {
        // draw
        $score = $r + 3;
//        echo "draw $score\n";
    } elseif (($rL === 'R' && $mL === 'S') || ($rL === 'S' && $mL === 'P') || ($rL === 'P' && $mL === 'R')) {
        // win
        $score = $r + 6;
//        echo "win  $score\n";
    } else {
        // lose
        $score = $r;
//        echo "lose $score\n";
    }
    $totalScore += $score;
}

echo 'Total score part 1: ', $totalScore, \PHP_EOL;
