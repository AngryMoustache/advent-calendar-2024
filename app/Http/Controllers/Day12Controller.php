<?php

namespace App\Http\Controllers;

use App\Entities\Map;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class Day12Controller extends Controller
{
    public Map $map;

    public int $counter = 0;

    public array $seen = [];
    public array $shapes = [];

    public function one(): int
    {
        return $this->getShapes()->reduce(function (int $carry, array $g, string $key) {
            return count($this->shapes[$key]) * collect($g)->pluck('perimeter')->sum() + $carry;
        }, 0);
    }

    public function two(): void
    {
        // No thanks
    }

    private function getShapes(): Collection
    {
        $this->data()->grid->each(function (array $row, int $y) {
           Collection::wrap($row)->each(function (string $char, int $x) use ($y) {
                if (isset($this->seen["{$x}-{$y}"])) {
                    return;
                }

                $this->getShape($x, $y, $char);
                $this->counter++;
            });
        });

        return Collection::wrap($this->shapes);
    }

    private function getShape(int $x, int $y, string $char): void
    {
        $plus = $this->map->getPlus($x, $y, withKeys: true);

        $this->shapes["{$char}-{$this->counter}"] ??= [];
        $this->shapes["{$char}-{$this->counter}"]["{$x}-{$y}"] ??= [];
        $this->shapes["{$char}-{$this->counter}"]["{$x}-{$y}"]['position'] = [$x, $y];
        $this->shapes["{$char}-{$this->counter}"]["{$x}-{$y}"]['perimeter'] = $plus
            ->filter(fn (?string $c) => $c !== $char)
            ->count();

        $plus = $plus->filter(fn (?string $c, string $key) =>
            $c === $char && ! isset($this->seen[$key])
        );

        $plus->each(function (string $c, string $key) {
            $this->seen[$key] = true;

            [$x, $y] = explode('-', $key);
            $this->getShape((int) $x, (int) $y, $c);
        });
    }

    private function data(): Map
    {
        $file = explode(PHP_EOL, File::get(public_path('inputs/12-1.txt'), 'r'));

        return $this->map = Map::make(
            collect($file)->filter()->map(fn (string $line) => str_split($line))
        );
    }
}
