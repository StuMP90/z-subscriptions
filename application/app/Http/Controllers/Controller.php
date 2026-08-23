<?php

namespace App\Http\Controllers;

use App\Models\Setting;

abstract class Controller
{
    protected function adminPageSize(): int
    {
        $perPage = (int) request()->input('per_page', 0);
        if ($perPage > 0) {
            return min($perPage, 1000);
        }

        return (int) (Setting::getValue('Default Admin List Page Size', 25));
    }
}
