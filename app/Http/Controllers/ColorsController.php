<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;

class ColorsController extends Controller
{
    public function getData()
    {
        $path = storage_path("/app/colors.json");
        $data = json_decode(File::get($path), true);

        return response()->json($data);
    }
}
