<?php

declare(strict_types=1);

$input = file_exists('input.txt') ? file_get_contents('input.txt') : null;

$input ??= <<<EXMAPLE
[1,1,3,1,1]
[1,1,5,1,1]

[[1],[2,3,4]]
[[1],4]

[9]
[[8,7,6]]

[[4,4],4,4]
[[4,4],4,4,4]

[7,7,7,7]
[7,7,7]

[]
[3]

[[[]]]
[[]]

[1,[2,[3,[4,[5,6,7]]]],8,9]
[1,[2,[3,[4,[5,6,0]]]],8,9]
EXMAPLE;

$input = explode("\n\n", rtrim($input, "\n"));
$input = array_map(static fn (string $pair) => explode("\n", $pair), $input);

// Part 1

class PacketList implements \Stringable
{
    public function __construct(
        public array $items = [],
        public ?self $parent = null,
    ) {
    }

    public function add(self|int $item): static
    {
        $this->items[] = $item;
        return $this;
    }

    public static function fromString(string $data): static
    {
        $root = $current = null;
        $integer = null;
        for ($i = 0, $length = strlen($data); $i < $length; $i++) {
            $token = $data[$i];
            // TODO: use peeking to get multi digit integers
            if ($token === '[') {
                $prev = $current;
                $current = new static([], $current);
                $prev?->add($current);
                $root ??= $current;
            } elseif ($token === ']') {
                if ($integer !== null) {
                    $current->add((int)$integer);
                    $integer = null;
                }
                $current = $current?->parent ?? $current;
            } elseif ($token === ',') {
                if ($integer !== null) {
                    $current->add((int)$integer);
                    $integer = null;
                }
            } elseif (ctype_digit($token)) {
                $integer .= $token;
            }
        }

        return $root;
    }

    public function __toString(): string
    {
        return '[' . implode(',', $this->items) . ']';
    }
}

function comparePacketLists(PacketList|int|null $left, PacketList|int|null $right): int
{
    if (is_int($left) && is_int($right)) {
        return $left <=> $right;
    }
    if ($left === null) {
        return -1;
    }
    if ($right === null) {
        return 1;
    }
    if (is_int($left)) {
        return comparePacketLists(new PacketList([$left]), $right);
    }
    if (is_int($right)) {
        return comparePacketLists($left, new PacketList([$right]));
    }

    assert($left instanceof PacketList && $right instanceof PacketList);

    $n = 0;
    while (true) {
        $leftItem = $left->items[$n] ?? null;
        $rightItem = $right->items[$n] ?? null;
        if ($leftItem === null && $rightItem === null) {
            return 0;
        }
        $result = comparePacketLists($leftItem, $rightItem);
        if ($result !== 0) {
            return $result;
        }
        $n++;
    }
}

$result = null;
foreach ($input as $n => [$left, $right]) {
    $leftPacket = PacketList::fromString($left);
    assert($left === (string)$leftPacket, 'Failed to parse left packet; ' . $left);
    $rightPacket = PacketList::fromString($right);
    assert($right === (string)$rightPacket, 'Failed to parse right packet; ' . $right);
    $comparison = comparePacketLists($leftPacket, $rightPacket);
    assert($comparison !== 0, 'Equal pairs???');
    if ($comparison === -1) {
        $result += $n + 1;
    }
}

echo 'Part 1: Sum of right indices: ', $result, \PHP_EOL;


// Part 2

$packets = array_map('PacketList::fromString', array_merge([], ...$input));
$dividerPackets = [PacketList::fromString('[[2]]'), PacketList::fromString('[[6]]')];
array_push($packets, ...$dividerPackets);

usort($packets, 'comparePacketLists');
$index1 = array_search($dividerPackets[0], $packets, true) + 1;
$index2 = array_search($dividerPackets[1], $packets, true) + 1;
$result = $index1 * $index2;

echo 'Part 2: Decoder key: ', $result, \PHP_EOL;
