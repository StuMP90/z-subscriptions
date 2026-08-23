<?php

namespace App\Http\Controllers;

use App\Models\Setting;

abstract class Controller
{
    protected function adminPageSize(): int
    {
        return (int) (Setting::where('key', 'Default Admin List Page Size')->value('value') ?? 25);
    }
}
