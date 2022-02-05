<?php

if (! function_exists('url_image')) {
    function url_image(string $link, string $options = 't_default')
    {
        return 'https://res.cloudinary.com/'.config('cloudinary.cloud_name').'/image/upload/' . $options . '/' . $link;
    }
}

if (! function_exists('readableSize')) {
    function readableSize($bytes)
    {
        $units = ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
