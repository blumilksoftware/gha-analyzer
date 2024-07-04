<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class OrganizationsController extends Controller {
    public function show() {
        return Inertia::render("Organizations", [
            "organizations": Organizations:all()
        ]);
    }
}