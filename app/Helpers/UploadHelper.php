<?php

namespace App\Helpers;

use Illuminate\Http\File;
use Illuminate\Support\Str;

class UploadHelper
{
    public static function storeFiles(array $files) : array
    {
        $paths = [];
        foreach ($files as $file) {
            $fileName = 'file_' . Str::random(10) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $directory = now()->format('m-Y');
            $destinationPath = 'public/uploads/' . $directory;
            $file->move(public_path($destinationPath), $fileName);
            $paths[] = $destinationPath . '/' . $fileName;
        }

        return $paths;
    }
}
