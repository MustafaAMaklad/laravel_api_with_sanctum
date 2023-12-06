<?php

namespace App\Helpers;

use Illuminate\Http\File;
use Illuminate\Support\Str;

class UploadHelper
{
    public static function storeFiles(array $files, string $directoty) : array
    {
        $paths = [];
        foreach ($files as $file) {
            $fileName = 'file_' . Str::random(10) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $destinationPath = 'uploads/' . $directoty;
            $file->storeAs($destinationPath, $fileName, 'public');
            $paths[] = $destinationPath . '/' . $fileName;
        }

        return $paths;
    }
}
