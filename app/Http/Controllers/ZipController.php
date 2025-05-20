<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ZipController extends Controller
{

    public function extract(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:zip|max:102400',
            'defaultFileName' => 'required|string',
        ]);

        $originalName = pathinfo($request->file('file')->getClientOriginalName(), PATHINFO_FILENAME);
        $extractPath = storage_path('app/public/html5/' . $originalName);

        $zip = new \ZipArchive();

        if ($zip->open($request->file('file')->getRealPath()) === true) {
            $zip->extractTo($extractPath);
            $zip->close();
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to open ZIP file'], 500);
        }

        $indexPath = $extractPath . DIRECTORY_SEPARATOR . $originalName . DIRECTORY_SEPARATOR . $request->defaultFileName;

        if (!File::exists($indexPath)) {
            $indexPath = $extractPath . DIRECTORY_SEPARATOR . $request->defaultFileName;

            if (!File::exists($indexPath)) {
                return response()->json(['success' => false, 'message' => 'default file not found'], 404);
            }
        }

        $relativePath = str_replace(storage_path('app/public/html5'), '', $indexPath);
        $relativePath = str_replace('\\', '/', $relativePath);

        return redirect(asset('storage/html5' . $relativePath));
    }
}
