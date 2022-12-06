<?php

declare(strict_types=1);

$input = file_exists('input.txt') ? file_get_contents('input.txt') : null;

$input ??= <<<EXMAPLE
mjqjpqmgbljsphdztnvjfqwrcgsmlb
bvwbjplbgvbhsrlpgdmjqwftvncz
nppdvjthqldpwncqszvftbrmjlhg
nznrnfrfntjfmvfwmzdfjlvtqnbhcprsg
zcfzfwzzqfrljwzlrfnpqdbhtmscgvjw
EXMAPLE;

$input = explode("\n", trim($input, "\n"));
$input = array_map('str_split', $input);

// Part 1

foreach ($input as $datastream) {
    $last = count($datastream) - 4;
    for ($i = 0; $i < $last; $i++) {
        $lastFour = array_slice($datastream, $i, 4);
        if (count(array_unique($lastFour)) === 4) {
            break;
        }
    }
    echo 'Part 1: Marked end: ', ($i + 4), \PHP_EOL;
}

// Part 2

foreach ($input as $datastream) {
    $last = count($datastream) - 14;
    for ($i = 0; $i < $last; $i++) {
        $lastFour = array_slice($datastream, $i, 14);
        if (count(array_unique($lastFour)) === 14) {
            break;
        }
    }
    echo 'Part 2: Marked end: ', ($i + 14), \PHP_EOL;
}
