<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;

class LogsController extends Controller
{
    public function getSampleLogs()
    {
        $path = storage_path("../resources/data/sampleLogs.json");
        $data = json_decode(File::get($path), true);

        return response()->json($data);
    }
}

?>