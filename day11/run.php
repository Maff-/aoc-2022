<?php

declare(strict_types=1);

$input = file_exists('input.txt') ? file_get_contents('input.txt') : null;

$input ??= <<<EXMAPLE
Monkey 0:
  Starting items: 79, 98
  Operation: new = old * 19
  Test: divisible by 23
    If true: throw to monkey 2
    If false: throw to monkey 3

Monkey 1:
  Starting items: 54, 65, 75, 74
  Operation: new = old + 6
  Test: divisible by 19
    If true: throw to monkey 2
    If false: throw to monkey 0

Monkey 2:
  Starting items: 79, 60, 97
  Operation: new = old * old
  Test: divisible by 13
    If true: throw to monkey 1
    If false: throw to monkey 3

Monkey 3:
  Starting items: 74
  Operation: new = old + 3
  Test: divisible by 17
    If true: throw to monkey 0
    If false: throw to monkey 1
EXMAPLE;

preg_match_all('/Monkey (?<monkey>\d+):.*?Starting items: (?<items>[\d, ]+).*?Operation: new = old (?<operator>.) (?<operand>\w+).*?Test: divisible by (?<test>\d+).*?If true: throw to monkey (?<true>\d+).*?If false: throw to monkey (?<false>\d+)/ms', $input, $matches, \PREG_SET_ORDER);

$monkeys = [];
foreach ($matches as $match) {
    $monkeys[(int)$match['monkey']] = [
        'items' => array_map('intval', explode(', ', $match['items'])),
        'operator' => $match['operator'],
        'operand' => ctype_digit($match['operand']) ? (int)$match['operand'] : $match['operand'],
        'testDivisor' => (int)$match['test'],
        'testTrue' => (int)$match['true'],
        'testFalse' => (int)$match['false'],
        'inspectCount' => 0,
    ];
}

// Part 1

for ($round = 1; $round <= 20; $round++) {
    foreach ($monkeys as $monkey => &$data) {
        foreach ($data['items'] as $itemWorryLevel) {
            // Inspect
            $operand = $data['operand'] === 'old' ? $itemWorryLevel : $data['operand'];
            $itemWorryLevel = match ($data['operator']) {
                '*' => $itemWorryLevel * $operand,
                '+' => $itemWorryLevel + $operand,
            };
            $itemWorryLevel = (int)floor($itemWorryLevel / 3);
            // Test
            $test = ($itemWorryLevel % $data['testDivisor']) === 0;
            // Throw
            $receivingMonkey = $test ? $data['testTrue'] : $data['testFalse'];
            $monkeys[$receivingMonkey]['items'][] = $itemWorryLevel;
            $data['inspectCount']++;
        }
        $data['items'] = [];
    }
    unset($data);
}

usort($monkeys, static fn ($a, $b): int => $b['inspectCount'] <=> $a['inspectCount']);
$result = $monkeys[0]['inspectCount'] * $monkeys[1]['inspectCount'];

echo 'Part 1: Monkey business after 20 rounds: ', $result, \PHP_EOL;


// Part 2

function gcd(int $a, int $b): int
{
    return $b === 0 ? $a : gcd($b, $a % $b);
}

function lcm(...$values): int
{
    $result = array_shift($values);
    foreach ($values as $val) {
        $result = ($val * $result) / gcd($val, $result);
    }

    return $result;
}

$lcm = lcm(...array_column($monkeys, 'testDivisor'));

for ($round = 1; $round <= 10000; $round++) {
    foreach ($monkeys as $monkey => &$data) {
        foreach ($data['items'] as $itemWorryLevel) {
            // Inspect
            $operand = $data['operand'] === 'old' ? $itemWorryLevel : $data['operand'];
            $itemWorryLevel = match ($data['operator']) {
                '*' => $itemWorryLevel * $operand,
                '+' => $itemWorryLevel + $operand,
            };
            // Test
            $test = ($itemWorryLevel % $data['testDivisor']) === 0;
            // Throw
            $receivingMonkey = $test ? $data['testTrue'] : $data['testFalse'];
            $monkeys[$receivingMonkey]['items'][] = $itemWorryLevel % $lcm;
            $data['inspectCount']++;
        }
        $data['items'] = [];
    }
    unset($data);
}

usort($monkeys, static fn ($a, $b): int => $b['inspectCount'] <=> $a['inspectCount']);
$result = $monkeys[0]['inspectCount'] * $monkeys[1]['inspectCount'];

echo 'Part 2: Monkey business after 10000 rounds: ', $result, \PHP_EOL;
