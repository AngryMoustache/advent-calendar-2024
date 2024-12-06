<?php

namespace App\Http\Controllers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class Day6Controller extends Controller
{
    public Collection $map;

    public array $start;
    public array $position;
    public string $direction;

    public function one(): int
    {
        $this->data();

        return collect($this->parsePath($this->map))
            ->pluck('position')
            ->unique()
            ->count();
    }

    public function two(): int
    {
        $this->data();

        $count = 0;

        foreach ($this->map as $y => $row) {
            foreach ($row as $x => $col) {
                if (in_array($col, ['#', '^'])) continue;

                $map = $this->map->toArray();

                $map[$y][$x] = '#';

                $count += (int) ($this->parsePath($map) === false);
            }
        }

        return $count;
    }

    private function parsePath(iterable $map, array $path = []): bool|array
    {
        $map = Collection::wrap($map);

        $this->start = $this->find('^');
        $this->direction = 'u';
        $this->position = $this->start;
        $seen = [];

        while (true) {
            $key = $this->key();
            $path[] = $key;

            if (in_array($key, $seen)) {
                return false;
            }

            $seen[] = $key;

            $newPosition = match ($this->direction) {
                'u' => [$this->position[0] - 1, $this->position[1]],
                'r' => [$this->position[0], $this->position[1] + 1],
                'd' => [$this->position[0] + 1, $this->position[1]],
                'l' => [$this->position[0], $this->position[1] - 1],
            };

            if (! isset($map[$newPosition[0]][$newPosition[1]])) {
                return $path;
            }

            if ($map[$newPosition[0]][$newPosition[1]] === '#') {
                $this->direction = match ($this->direction) {
                    'u' => 'r',
                    'r' => 'd',
                    'd' => 'l',
                    'l' => 'u',
                };
            } else {
                $this->position = $newPosition;
            }
        }
    }

    private function key(): string
    {
        return implode('-', [...$this->position, $this->direction]);
    }

    private function find(string $find): array
    {
        return $this->map->map(function (array $line, int $y) use ($find) {
            return collect($line)
                ->map(fn (string $char, int $x) => ($char === $find) ? [$y, $x] : null)
                ->filter();
        })->flatten()->toArray();
    }

    private function data()
    {
        $file = explode(PHP_EOL, File::get(public_path('inputs/6-1.txt'), 'r'));

        return $this->map = collect($file)
            ->filter()
            ->map(fn (string $line) => str_split($line));
    }
}
