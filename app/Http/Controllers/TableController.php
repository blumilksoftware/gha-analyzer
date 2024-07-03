<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\ColorsService;
use Inertia\Inertia;

class TableController extends Controller
{
    public function __construct(
        protected ColorsService $colorsService,
    ) {}

    public function show()
    {
        return Inertia::render("Table", [
            "colors" => $this->colorsService->getColors(),
        ]);
    }
}
