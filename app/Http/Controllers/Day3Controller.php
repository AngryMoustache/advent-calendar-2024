<?php

namespace App\Http\Controllers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class Day3Controller extends Controller
{
    public function one(): int
    {
        return $this->data()->mul();
    }

    public function two(): int
    {
        return $this->data()
            ->map(fn (string $line) => preg_replace('/don\'t\(\)(.*?)do\(\)/', '', $line))
            ->mul();
    }

    private function data()
    {
        Collection::macro('mul', function (): int {
            preg_match_all('/mul\((\d+),(\d+)\)/', $this->items[0] ?? '', $matches);
            $matches = array_map(null, ...[$matches[1], $matches[2]]); // Transpose

            return collect($matches)->map(fn (array $match) => $match[0] * $match[1])->sum();
        });

        return collect(str_replace(PHP_EOL, '', File::get(public_path('inputs/3-1.txt'), 'r')));
    }
}
