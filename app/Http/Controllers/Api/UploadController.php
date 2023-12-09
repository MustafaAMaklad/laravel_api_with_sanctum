<?php

namespace App\Http\Controllers\Api;

use App\Helpers\UploadHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UploadController extends Controller
{
    public function upload(Request $request) {

        $validator = Validator::make($request->allFiles(), [
            'files' => 'required|array|min:1',
            'files.*' => 'file|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        $links = UploadHelper::storeFiles($request->file('files'));

        return response()->json([
            'status' => true,
            'message' => 'Files were uploaded successfully',
            'data' => $links,
        ]);
    }
}
