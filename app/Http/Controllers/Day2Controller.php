<?php

namespace App\Http\Controllers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class Day2Controller extends Controller
{
    public int $dampener;

    public function one()
    {
        return $this->check(0)->count();
    }

    public function two()
    {
        return $this->check(1)->count();
    }

    public function check(int $allowedMistakes)
    {
        return $this->data()->filter(function (Collection $line) use ($allowedMistakes) {
            $this->dampener = $allowedMistakes;

            $delta = $line->unique()->take(2)->values();
            $delta = ($delta[0] - $delta[1]) < 0;

            for ($index = 0; $index < ($line->count() - 1); $index++) {
                $newDelta = $line[$index] - $line[$index + 1];

                if (
                    ! in_array(abs($newDelta), range(1, 3))
                    || ($newDelta < 0) !== $delta
                ) {
                    if ($this->dampener > 0) {
                        $this->dampener--;

                        // Remove the wrong level and try again from the start
                        $line = $line->forget($index)->values();
                        $index = 0;
                    } else {
                        return false;
                    }
                }
            }

            return true;
        });
    }

    private function data()
    {
        $file = File::get(public_path('inputs/2-1.txt'), 'r');

        return collect(explode(PHP_EOL, $file))
            ->filter()
            ->map(fn (string $line) => collect(explode(' ', $line))
                ->map(fn (string $number) => (int) $number)
            );
    }
}
