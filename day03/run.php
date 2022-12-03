<?php

declare(strict_types=1);

$input = file_exists('input.txt') ? file_get_contents('input.txt') : null;

$input ??= <<<EXMAPLE
vJrwpWtwJgWrhcsFMMfFFhFp
jqHRNqRjqzjGDLGLrsFMfFZSrLrFZsSL
PmmdzqPrVvPwwTWBwg
wMqvLMZHhHMvwLHjbvcjnnSBnvTQFn
ttgJtRGJQctTZtZT
CrZsJsPPZsGzwwsLwLmpwMDw
EXMAPLE;

$input = explode("\n", trim($input));
$input = array_map(static fn($rucksack) => array_chunk(str_split($rucksack), strlen($rucksack) / 2), $input);

// Part 1

$priorities = array_combine(array_merge(range('a', 'z'), range('A', 'Z')), range(1, 52));

$sum = 0;
foreach ($input as [$compartment1, $compartment2]) {
    $items = array_intersect(array_unique($compartment1), array_unique($compartment2));
    foreach ($items as $item) {
        $priority = $priorities[$item];
        $sum += $priority;
    }
}

echo 'Part 1; Sum of priorities: ', $sum, \PHP_EOL;


// Part 2

$groups = array_chunk($input, 3);

$sum = 0;
foreach ($groups as $group) {
    $badge = current(array_intersect(...array_map(static fn($compartments) => array_merge(...$compartments), $group)));
    $sum += $priorities[$badge];
}

echo 'Part 2; Sum of badge priorities: ', $sum, \PHP_EOL;
