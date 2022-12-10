<?php

declare(strict_types=1);

$input = file_exists('input.txt') ? file_get_contents('input.txt') : null;

$input ??= <<<EXMAPLE
addx 15
addx -11
addx 6
addx -3
addx 5
addx -1
addx -8
addx 13
addx 4
noop
addx -1
addx 5
addx -1
addx 5
addx -1
addx 5
addx -1
addx 5
addx -1
addx -35
addx 1
addx 24
addx -19
addx 1
addx 16
addx -11
noop
noop
addx 21
addx -15
noop
noop
addx -3
addx 9
addx 1
addx -3
addx 8
addx 1
addx 5
noop
noop
noop
noop
noop
addx -36
noop
addx 1
addx 7
noop
noop
noop
addx 2
addx 6
noop
noop
noop
noop
noop
addx 1
noop
noop
addx 7
addx 1
noop
addx -13
addx 13
addx 7
noop
addx 1
addx -33
noop
noop
noop
addx 2
noop
noop
noop
addx 8
noop
addx -1
addx 2
addx 1
noop
addx 17
addx -9
addx 1
addx 1
addx -3
addx 11
noop
noop
addx 1
noop
addx 1
noop
noop
addx -13
addx -19
addx 1
addx 3
addx 26
addx -30
addx 12
addx -1
addx 3
addx 1
noop
noop
noop
addx -9
addx 18
addx 1
addx 2
noop
noop
addx 9
noop
noop
noop
addx -1
addx 2
addx -37
addx 1
addx 3
noop
addx 15
addx -21
addx 22
addx -6
addx 1
noop
addx 2
addx 1
noop
addx -10
noop
noop
addx 20
addx 1
addx 2
addx 2
addx -6
addx -11
noop
noop
noop
EXMAPLE;

$input = explode("\n", trim($input, "\n"));
$input = array_map(static fn (string $line): array => $line === 'noop' ? ['noop', null] : ['addx', (int)explode(' ', $line)[1]], $input);

$instructionCycles = ['noop' => 1, 'addx' => 2];

$result = 0;
$resultCycles = range(20, 220, 40);
$lastResultCycle = end($resultCycles);
reset($resultCycles);

$x = 1;
$cycle = 0;
foreach ($input as [$instruction, $arg]) {
    for ($i = 0; $i < $instructionCycles[$instruction]; $i++) {
        $cycle++;
//        printf("%3d: x=%d\n", $cycle, $x);
        if (in_array($cycle, $resultCycles, true)) {
            $result += $cycle * $x;
            if ($cycle === $lastResultCycle) {
                break 2;
            }
        }
    }
//    echo implode(' ', [$instruction, $arg]), \PHP_EOL;
    if ($instruction === 'addx') {
        $x += $arg;
    }
}

echo 'Part 1: ', $result, \PHP_EOL;
