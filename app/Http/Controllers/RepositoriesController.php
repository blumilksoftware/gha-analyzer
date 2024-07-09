<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Repository;
use App\Services\ColorsService;
use Inertia\Inertia;

class RepositoriesController extends Controller
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

        return Inertia::render("Repositories", [
            "colors" => $this->colors,
            "repositoriesPROPS" => Repository::all(),
        ]);
    }

    private function getColors()
    {
        return $this->colorsService->getColors();
    }
}
