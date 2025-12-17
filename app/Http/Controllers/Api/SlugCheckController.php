<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Business;

class SlugCheckController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        if (! $request->filled('slug')) {
            return response()->json([
                'message' => 'Bad request: missing required parameter',
                'errors' => ['slug' => 'This field is required.']
            ], 400);
        }

        $available = Business::query()->where('slug', $request->slug)->doesntExist();

        return response()->json([
            'available' => $available,
        ]);
    }
}
