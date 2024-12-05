<?php

namespace App\Http\Controllers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class Day5Controller extends Controller
{
    public Collection $rules;
    public Collection $updates;

    public function one(): int
    {
        return $this->data()
            ->filter(fn (Collection $update) => $this->getBrokenRules($update)->isEmpty())
            ->map(fn (Collection $update) => $update->get($update->count() / 2))
            ->sum();
    }

    public function two(): int
    {
        return $this->data()
            ->reject(fn (Collection $update) => $this->getBrokenRules($update)->isEmpty())
            ->map(function (Collection $update) {
                while ($this->getBrokenRules($update)->isNotEmpty()) {
                    $update = $this->fixRules($update, $this->getBrokenRules($update));
                }

                return $update;
            })
            ->map(fn (Collection $update) => $update->get($update->count() / 2))
            ->sum();
    }

    private function getBrokenRules(Collection $update): Collection
    {
        return $this->rules->map(function (Collection $rule) use ($update) {
            if ($rule->intersect($update)->count() !== 2) {
                return null;
            }

            $key = $update->search($rule->last());

            return $key
                ? ($update->slice(0, $key)->contains($rule->first()) ? null : $rule)
                : $rule;
        })->filter();
    }

    private function fixRules(Collection $update, Collection $rules): Collection
    {
        $rules->each(function (Collection $rule) use (&$update) {
            $update = $update
                ->reject(fn (int $value) => $value === $rule->last())
                ->merge($rule->last());
        });

        return $update;
    }

    private function data()
    {
        $file = explode(PHP_EOL . PHP_EOL, File::get(public_path('inputs/5-1.txt'), 'r'));

        [$this->rules, $this->updates] = collect($file)->map(function (string $data, int $key) {
            $data = collect(explode(PHP_EOL, $data))->filter();
            $separator = ($key === 0) ? '|' : ',';

            return $data
                ->map(fn (string $item) => collect(explode($separator, $item)))
                ->map(fn (Collection $item) => $item->map(fn (string $value) => (int) $value));
        });

        return $this->updates;
    }
}
