<?php

namespace App\Http\Controllers;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class Day7Controller extends Controller
{
    public function one(): int
    {
        return $this->data()
            ->map(fn (array $line) =>  $this->operate($line, ['+', '*']))
            ->sum();
    }

    public function two(): int
    {
        return $this->data()
            ->map(fn (array $line) =>  $this->operate($line, ['+', '*', '||']))
            ->sum();
    }

    public function operate(array $line, array $operators): int
    {
        $equations = [[$line[1][0]]];

        for ($i = 1; $i < count($line[1]); $i++) {
            $rightSide = $line[1][$i];
            $leftSides = $equations[$i - 1];
            $equations[$i] = [];

            foreach ($leftSides as $left) {
                foreach ($operators as $operator) {
                    $result = match ($operator) {
                        '+' => $left + $rightSide,
                        '*' => $left * $rightSide,
                        '||' => (int) "{$left}{$rightSide}",
                    };

                    if ($result <= $line[0]) {
                        $equations[$i][] = $result;
                    }
                }
            }
        }

        $array = Arr::last($equations);

        return (int) collect($array)->contains($line[0]) ? $line[0] : 0;
    }

    private function data(): Collection
    {
        $file = explode(PHP_EOL, File::get(public_path('inputs/7-1.txt'), 'r'));

        return collect($file)->filter()->map(function (string $line) {
            $parts = explode(': ', $line);
            return [(int) $parts[0], collect(explode(' ', $parts[1]))->map(fn (string $l) => (int) $l)->toArray()];
        });
    }
}
