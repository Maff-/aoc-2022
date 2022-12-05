<?php

declare(strict_types=1);

$input = file_exists('input.txt') ? file_get_contents('input.txt') : null;

$input ??= <<<EXMAPLE
    [D]    
[N] [C]    
[Z] [M] [P]
 1   2   3 

move 1 from 2 to 1
move 3 from 1 to 3
move 2 from 2 to 1
move 1 from 1 to 2
EXMAPLE;

[$drawing, $procedures] = explode("\n\n", trim($input, "\n"));

$lines = explode("\n", $drawing);
$cols = array_map('intval', preg_split('/\s+/', trim($lines[array_key_last($lines)])));
$lines = array_reverse(array_slice($lines, 0, -1));
$stack = array_fill_keys($cols, []);
foreach ($lines as $line) {
    $foo = str_split($line, 4);
    foreach ($foo as $n => $str) {
        if (preg_match('/\[([A-Z]+)]/', $str, $match)) {
            $stack[$n + 1][] = $match[1];
        }
    }
}

$procedures = array_map(static function (string $line): array {
    return preg_match('/move (\d+) from (\d+) to (\d+)/', $line, $match)
        ? array_map('intval', array_slice($match, 1))
        : [];
}, explode("\n", $procedures));

// Part 1

$top = '';

foreach ($procedures as [$count, $from, $to]) {
    // TODO: use array_slice+array_reverse to move all containers at once?
    for ($n = 0; $n < $count; $n++) {
        $container = array_pop($stack[$from]);
        $stack[$to][] = $container;
    }
}

foreach ($stack as $col => $containers) {
    $top .= end($containers);
}

echo 'Part 1; ...: ', $top, \PHP_EOL;
