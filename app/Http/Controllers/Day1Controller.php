<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;

class Day1Controller extends Controller
{
    public function one()
    {

    }

    public function two()
    {

    }

    private function data()
    {
        $file = File::get(public_path('inputs/1-1.txt'), 'r');

        return collect(explode(PHP_EOL, $file))->filter();
    }
}
