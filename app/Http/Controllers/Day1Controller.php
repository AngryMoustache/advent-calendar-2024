<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;

class Day1Controller extends Controller
{
    public function one()
    {
        $lists = $this->data();

        return $lists[0]->keys()
            ->map(fn (int $index) => abs($lists[0][$index] - $lists[1][$index]))
            ->sum();
    }

    public function two()
    {
        $lists = $this->data();

        return $lists[0]->reduce(fn (int $carry, int $value) => $carry + abs(
            $value * ($lists[1]->countBy()[$value] ?? 0)
        ), 0);
    }

    private function data()
    {
        $file = File::get(public_path('inputs/1-1.txt'), 'r');

        $lists = collect(explode(PHP_EOL, $file))
            ->filter()
            ->map(fn (string $line) => explode('   ', $line));

        return collect(range(0, 1))->map(fn (int $index) =>
            $lists->pluck($index)->map(fn (int $number) => $number)->sort()->values()
        );
    }
}
