<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class CacheController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Cache/Index');
    }
}
