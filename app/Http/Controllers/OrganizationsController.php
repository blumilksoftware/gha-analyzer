<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Organization;

use Illuminate\Http\Request;

class OrganizationsController extends Controller {
    public function show() {
        return Inertia::render("Organizations", [
            "organizations" => Organization::all()
        ]);
    }
}