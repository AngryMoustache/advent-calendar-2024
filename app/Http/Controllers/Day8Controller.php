<?php

namespace App\Http\Controllers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class Day8Controller extends Controller
{
    public int $size;

    public array $antinodes = [];

    public function one(): int
    {
        return $this->createAntinodes()->count();
    }

    public function two(): int
    {
        return $this->createAntinodes(infinite: true)->count();
    }

    private function createAntinodes(bool $infinite = false): Collection
    {
        $this->data()->each(function (Collection $nodes) use ($infinite) {
            $nodes->each(function (array $node, int $key) use ($nodes, $infinite) {
                $nodes->except($key)->each(function (array $other) use ($node, $infinite) {
                    [$xDiff, $yDiff] = [$node['x'] - $other['x'], $node['y'] - $other['y']];
                    $x = $node['x'];
                    $y = $node['y'];

                    if ($infinite) {
                        while ($this->inRange($x, $y)) {
                            $this->antinodes["{$x}-{$y}"] = true;

                            $x += $xDiff;
                            $y += $yDiff;
                        }
                    }  else {
                        $x += $xDiff;
                        $y += $yDiff;

                        $this->antinodes["{$x}-{$y}"] = $this->inRange($x, $y);
                    }
                });
            });
        });

        return collect($this->antinodes)->filter();
    }

    private function inRange(int $x, int $y): bool
    {
        return in_array($x, range(0, $this->size - 1)) && in_array($y, range(0, $this->size - 1));
    }

    private function data(): Collection
    {
        $file = collect(explode(PHP_EOL, File::get(public_path('inputs/8-1.txt'), 'r')))
            ->filter();

        $this->size = $file->count();

        return $file
            ->map(fn (string $line) => str_split($line))
            ->map(fn (array $row, int $y) => collect($row)->map(fn (string $column, int $x) => [
                'x' => $x,
                'y' => $y,
                'frequency' => (string) $column,
            ]))
            ->flatten(1)
            ->reject(fn (array $node) => $node['frequency'] === '.')
            ->groupBy('frequency');
    }
}
