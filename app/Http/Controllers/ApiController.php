<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function checkSlug(Request $request)
    {
        if (!$request->has('slug')) {
            return response()->json([
                'message' => 'slug already exists',
            ]);
        }

        $exists = Business::query()->where('slug', $request->slug)->exists();

        return response()->json([
            'available' => !$exists,
            'slug' => $request->slug,
        ]);
    }
}
