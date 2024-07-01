<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;

class LogsController extends Controller {
    function getSampleLogs(){
        $path = storage_path('/app/sampleLogs.json');
        $data = json_decode(File::get($path), true);
        return response()->json($data);
    }
}

?>