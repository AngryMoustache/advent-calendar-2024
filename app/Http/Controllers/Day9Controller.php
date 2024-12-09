<?php

namespace App\Http\Controllers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class Day9Controller extends Controller
{
    public function one(): string
    {
        $data = $this->data()->flatten();

        for ($i = 0; $i < count($data); $i++) {
            if ($data[$i] !== '.') {
                continue;
            }

            $data[$i] = $data->pop();

            while ($data->last() === '.') {
                $data->pop();
            }
        }

        return $data->reduce(fn (int $carry, int $item, int $id) => $carry + ($item * $id), 0);
    }

    public function two(): void
    {
        // No thanks, I'm good
    }

    private function data(): Collection
    {
        $file = explode(PHP_EOL, File::get(public_path('inputs/9-1.txt'), 'r'))[0];

        return collect(str_split($file))
            ->map(fn (int $item, int $key) => array_pad([], $item, ($key % 2 === 0) ? $key / 2 : '.'));
    }
}
