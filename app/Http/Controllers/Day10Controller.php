<?php

namespace App\Http\Controllers;

use App\Entities\Map;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class Day10Controller extends Controller
{
    public Map $map;

    public Collection $hikers;

    public function one(): int
    {
        return $this->data()
            ->countBy(fn (array $hiker) => implode('-', $hiker['start']) . '--' . implode('-', $hiker['position']))
            ->count();
    }

    public function two(): int
    {

        return $this->data()->count();
    }

    private function moveHikers(): Collection
    {
        return $this->hikers = $this->hikers->transform(function (array $hiker) {
            [$x, $y] = $hiker['position'];

            return $this->map->getPlus($x, $y)
                ->filter(fn (?int $char) => $char === $hiker['height'] + 1)
                ->keys()
                ->map(fn (int $direction) => [
                    'start' => $hiker['start'],
                    'height' => $hiker['height'] + 1,
                    'position' => match ($direction) {
                        0 => [$x, $y - 1],
                        1 => [$x + 1, $y],
                        2 => [$x, $y + 1],
                        3 => [$x - 1, $y],
                    },
                ]);
        })->flatten(1);
    }

    private function data(): Collection
    {
        $file = explode(PHP_EOL, File::get(public_path('inputs/10-1.txt'), 'r'));

        $this->map = Map::make(
            collect($file)->filter()->map(fn (string $line) => str_split($line))
        );

        $this->hikers = $this->map->findCoordsFor('0')->map(fn (array $coord) => [
            'height' => 0,
            'position' => $coord,
            'start' => $coord,
        ]);

        for ($i = 0; $i < 9; $i++) {
            $this->moveHikers();
        }

        return $this->hikers;
    }
}
