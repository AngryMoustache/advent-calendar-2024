<?php

namespace App\Http\Controllers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class Day4Controller extends Controller
{
    private Collection $data;

    public function one(): int
    {
        return $this->findCoordsFor('X')->reduce(function (int $carry, array $coords) {
            [$x, $y] = $coords;

            // Star search
            return $carry + collect([
                $this->get([$x - 1, $y - 1], [$x - 2, $y - 2], [$x - 3, $y - 3]),
                $this->get([$x, $y - 1], [$x, $y - 2], [$x, $y - 3]),
                $this->get([$x + 1, $y - 1], [$x + 2, $y - 2], [$x + 3, $y - 3]),
                $this->get([$x - 1, $y], [$x - 2, $y], [$x - 3, $y]),
                $this->get([$x + 1, $y], [$x + 2, $y], [$x + 3, $y]),
                $this->get([$x - 1, $y + 1], [$x - 2, $y + 2], [$x - 3, $y + 3]),
                $this->get([$x, $y + 1], [$x, $y + 2], [$x, $y + 3]),
                $this->get([$x + 1, $y + 1], [$x + 2, $y + 2], [$x + 3, $y + 3]),
            ])
                ->filter(fn (Collection $chars) => $chars->join('') === 'MAS')
                ->count();
        }, 0);
    }

    public function two(): int
    {
        return $this->findCoordsFor('A')->reduce(function (int $carry, array $coords) {
            [$x, $y] = $coords;

            // X search
            $check = collect([
                $this->get([$x - 1, $y - 1], [$x + 1, $y + 1]),
                $this->get([$x - 1, $y + 1], [$x + 1, $y - 1]),
            ])
                ->filter(fn (Collection $part) => $part->sort()->join('') === 'MS')
                ->count();

            return $carry + ((int) $check === 2);
        }, 0);
    }

    private function findCoordsFor(string $find): Collection
    {
        return $this->data()->map(function (array $line, int $y) use ($find) {
            return collect($line)
                ->map(fn (string $char, int $x) => ($char === $find) ? [$x, $y] : null)
                ->filter();
        })->flatten(1);
    }

    private function get(...$coords): Collection
    {
        return Collection::wrap($coords)
            ->map(fn (array $coord): ?string => $this->data[$coord[1]][$coord[0]] ?? null);
    }

    private function data()
    {
        $file = File::get(public_path('inputs/4-1.txt'), 'r');

        return $this->data = collect(explode(PHP_EOL, $file))
            ->filter()
            ->map(fn (string $line) => collect(str_split($line))->toArray());
    }
}
