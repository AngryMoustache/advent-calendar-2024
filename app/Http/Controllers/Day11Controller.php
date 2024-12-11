<?php

namespace App\Http\Controllers;

use App\Entities\Map;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class Day11Controller extends Controller
{
    public Collection $stones;

    public function one(): int
    {
        return $this->blinks(25)->sum();
    }

    public function two(): int
    {
        return $this->blinks(75)->sum();
    }

    public function blinks(int $amount): Collection
    {
        $this->data();

        for ($i = 0; $i < $amount; $i++) {
            $this->blink();
        }

        return $this->stones;
    }

    public function blink(): Collection
    {
        $newStones = [];
        $this->stones->each(function (int $amount, int $stone) use (&$newStones) {
            if ($stone === 0) {
                $newStones[1] ??= 0;
                $newStones[1] += $amount;
                return;
            }

            if (strlen($stone) % 2 === 0) {
                foreach (str_split($stone, strlen($stone) / 2) as $part) {
                    $newStones[$part] ??= 0;
                    $newStones[$part] += $amount;
                }

                return;
            }

            $stone *= 2024;
            $newStones[$stone] ??= 0;
            $newStones[$stone] += $amount;
        });

        return $this->stones = collect($newStones);
    }

    private function data(): Collection
    {
        $file = explode(PHP_EOL, File::get(public_path('inputs/11-1.txt'), 'r'));

        return $this->stones = collect($file)
            ->filter()
            ->map(fn (string $line) => explode(' ', $line))
            ->flatten()
            ->mapWithKeys(fn (int $stone) => [$stone => 1]);
    }
}
