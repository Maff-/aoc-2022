<?php

declare(strict_types=1);

$input = file_exists('input.txt') ? file_get_contents('input.txt') : null;

$input ??= <<<EXMAPLE
$ cd /
$ ls
dir a
14848514 b.txt
8504156 c.dat
dir d
$ cd a
$ ls
dir e
29116 f
2557 g
62596 h.lst
$ cd e
$ ls
584 i
$ cd ..
$ cd ..
$ cd d
$ ls
4060174 j
8033020 d.log
5626152 d.ext
7214296 k
EXMAPLE;

$input = explode("\n", trim($input, "\n"));

class File implements \Stringable {

    public function __construct(
        readonly public string $name,
        readonly public int $size,
        readonly public Dir $dir,
    ) {
    }

    public function __toString(): string
    {
        return $this->name;
    }
}

class Dir implements \Stringable, \IteratorAggregate {

    /** @var static[]|File[] */
    private array $content = [];

    public function __construct(
        readonly public string $name,
        readonly public ?self $parent,
    ) {
    }

    public function cd($dir): static
    {
        return $this->content[$dir] ??= new static($dir, $this);
    }

    public function addDir($name): void
    {
        if (array_key_exists($name, $this->content)) {
            throw new \InvalidArgumentException(sprintf('A file or dir named "%s" already exists', $name));
        }
        $this->content[$name] = new Dir($name, $this);
    }

    public function addFile($name, $size): void
    {
        if (array_key_exists($name, $this->content)) {
            throw new \InvalidArgumentException(sprintf('A file or dir named "%s" already exists', $name));
        }
        $this->content[$name] = new File($name, $size, $this);
    }

    public function getTotalSize(): int
    {
        $total = 0;

        foreach ($this->content as $dirOrFile) {
            $total += match ($dirOrFile::class) {
                static::class => $dirOrFile->getTotalSize(),
                File::class => $dirOrFile->size,
            };
        }

        return $total;
    }

    public function dirs(): array
    {
        return array_filter($this->content, static fn(Dir|File $dirOrFile): bool => $dirOrFile instanceof Dir);
    }

    public function files(): array
    {
        return array_filter($this->content, static fn (Dir|File $dirOrFile): bool => $dirOrFile instanceof File);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->content);
    }

    public function __toString(): string
    {
        return $this->name . '/';
    }
}

// Init filesystem

$root = new Dir('/', null);
$dir = $root;
foreach ($input as $line) {
    if (str_starts_with($line, '$')) {
        [$command, $arg] = array_pad(explode(' ', substr($line, 2), 2), 2, null);
        if ($command === 'cd') {
            $dir = match ($arg) {
                '/' => $root,
                '..' => $dir->parent,
                default => $dir->cd($arg),
            };
        }
    } else {
        // ls output
        [$size, $filename] = explode(' ', $line);
        if ($size === 'dir') {
            $dir->addDir($filename);
        } else {
            $dir->addFile($filename, (int)$size);
        }
    }
}

// Part 1

function sumTotalSizes (Dir $dir, ?callable $filter = null, ?int &$sum = null): int {
    $sum ??= 0;
    if (!$filter || $filter($dir)) {
        $sum += $dir->getTotalSize();
    }
    foreach ($dir->dirs() as $subDir) {
        sumTotalSizes($subDir, $filter, $sum);
    }
    return $sum;
}

$sum = sumTotalSizes($root, static fn(Dir $dir): bool => $dir->getTotalSize() <= 100000);

echo 'Part 1: sum of the total sizes: ', $sum, \PHP_EOL;
