<?php

namespace App\Entities;

use Illuminate\Support\Collection;

class Map
{
    public Collection $grid;

    public static function make(Collection $grid)
    {
        return new static($grid);
    }

    public function __construct(Collection $grid)
    {
        $this->grid = $grid;
    }

    public function findCoordsFor(string $find): Collection
    {
        return $this->grid->map(function (array $line, int $y) use ($find) {
            return collect($line)
                ->map(fn (string $char, int $x) => ($char === $find) ? [$x, $y] : null)
                ->filter();
        })->flatten(1);
    }

    public function getPlus(int $x, int $y, bool $withKeys = false): Collection
    {
        return$this->get([$x, $y - 1], [$x + 1, $y], [$x, $y + 1], [$x - 1, $y])
            ->when($withKeys, fn (Collection $c) => collect([
                $x . '-' . ($y - 1),
                ($x + 1) . '-' . $y,
                $x . '-' . ($y + 1),
                ($x - 1) . '-' . $y,
            ])->combine($c));
    }

    public function get(...$coords): Collection
    {
        return Collection::wrap($coords)
            ->map(fn (array $coord): ?string => $this->grid[$coord[1]][$coord[0]] ?? null);
    }
}
