<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;

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
                'errors' => ['slug' => 'This field is required.'],
            ], 400);
        }

        $query = Business::query()->where('slug', $request->slug);

        // Игнорируем текущий бизнес при редактировании
        if ($request->filled('business_id')) {
            $query->where('id', '!=', $request->business_id);
        }

        $available = $query->doesntExist();

        return response()->json([
            'available' => $available,
        ]);
    }
}
