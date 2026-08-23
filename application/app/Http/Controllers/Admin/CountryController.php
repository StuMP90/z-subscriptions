<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class CountryController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Countries/Index');
    }
}
