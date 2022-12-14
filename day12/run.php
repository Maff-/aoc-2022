<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use JMGQ\AStar\AStar;
use JMGQ\AStar\DomainLogicInterface;
use JMGQ\AStar\Node\NodeIdentifierInterface;

$input = file_exists('input.txt') ? file_get_contents('input.txt') : null;

$input ??= <<<EXMAPLE
Sabqponm
abcryxxl
accszExk
acctuvwj
abdefghi
EXMAPLE;

$input = array_map('str_split', explode("\n", rtrim($input, "\n")));

class Tile implements NodeIdentifierInterface, \Stringable
{
    public readonly int $h;

    public function __construct(
        public readonly int $x,
        public readonly int $y,
        public readonly string $val,
    ) {
        $this->h = ord($val) - ord('a');
    }

    public function getUniqueNodeId(): string
    {
        return $this->x . ',' . $this->y;
    }

    public function __toString(): string
    {
        return sprintf('[%d,%d](%s)', $this->x, $this->y, $this->val);
    }
}

$height = count($input);
$width = count($input[0]);
$endPos = $startPos = null;
/** @var Tile[][] $map */
$map = array_fill_keys(array_keys($input), array_fill(0, $width, null));
foreach ($input as $y => $row) {
    foreach ($row as $x => $value) {
        if ($value === 'S') {
            $startPos = $map[$y][$x] = new Tile($x, $y, 'a');
        } elseif ($value === 'E') {
            $endPos = $map[$y][$x] = new Tile($x, $y, 'z');
        } else {
            $map[$y][$x] = new Tile($x, $y, $value);
        }
    }
}

$logic = new class ($map) implements DomainLogicInterface
{
    public const DIRECTIONS = [
        '>' => [+1, 0], // right
        '^' => [0, -1], // up
        'v' => [0, +1], // down
        '<' => [-1, 0], // left
    ];

    public function __construct(
        private readonly array $map,
    ) {
    }

    /**
     * @param Tile $node
     */
    public function getAdjacentNodes(mixed $node): iterable
    {
        $nodes = [];

        foreach (static::DIRECTIONS as [$dX, $dY]) {
            $x = $node->x + $dX;
            $y = $node->y + $dY;
            /** @var ?Tile $tile */
            $tile = $this->map[$y][$x] ?? null;
            if ($tile === null) {
                continue;
            }
            if ($tile->h > $node->h + 1) {
                continue;
            }
            $nodes[] = $tile;
        }

        return $nodes;
    }

    /**
     * @param Tile $node
     */
    public function calculateRealCost(mixed $node, mixed $adjacent): float|int
    {
        return 1;
    }

    /**
     * @param Tile $fromNode
     * @param Tile $toNode
     */
    public function calculateEstimatedCost(mixed $fromNode, mixed $toNode): float|int
    {
//        return $this->manhattanDistance($fromNode, $toNode);
        return $this->euclideanDistance($fromNode, $toNode);
    }

    private function manhattanDistance(Tile $a, Tile $b): int
    {
        return abs($b->x - $a->x) + abs($b->y - $a->y);
    }

    private function euclideanDistance(Tile $a, Tile $b): float
    {
        return sqrt((abs($b->x - $a->x) ** 2) + (abs($b->y - $a->y) ** 2));
    }
};

$aStar = new AStar($logic);
/** @var Tile[] $path */
$path = $aStar->run($startPos, $endPos);

$result = count($path) - 1;

echo 'Part 1: steps taken: ', $result, \PHP_EOL;


// Part 2 -- Brute force al possibilities

$shortestPath = null;

foreach ($map as $row) {
    foreach ($row as $tile) {
        if ($tile->h === 0) {
            $path = $aStar->run($tile, $endPos);
            if (!$path) {
                continue;
            }
            $shortestPath ??= $path;
            if (count($path) < count($shortestPath)) {
                $shortestPath = $path;
            }
        }
    }
}

$result = count($shortestPath) - 1;

echo 'Part 2: steps taken in shortest path: ', $result, \PHP_EOL;
