<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\ColorsService;
use Inertia\Inertia;

class AuthorsController extends Controller
{
    protected array $colors;

    public function __construct(
        protected ColorsService $colorsService,
    ) {
        $this->colors = [];
    }

    public function show()
    {
        $this->colors = $this->getColors();

        return Inertia::render("Authors", [
            "colors" => $this->colors,
        ]);
    }

    private function getColors()
    {
        return $this->colorsService->getColors();
    }
}
