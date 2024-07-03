<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\File;

class ColorsService
{
    protected string $colorsFilePath;

    public function __construct()
    {
        $this->colorsFilePath = "../resources/data/colors.json";
    }

    public function getColors(): array
    {
        if (File::exists($this->colorsFilePath)) {
            $fileContent = File::get($this->colorsFilePath);
            $data = json_decode($fileContent, true);

            return $data;
        }

        throw new \Exception("File not found: $this->colorsFilePath");
    }
}
