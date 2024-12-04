<?php

if (! function_exists('transpose')) {
    function transpose(array $array)
    {
        return array_map(null, ...$array);
    }
}
