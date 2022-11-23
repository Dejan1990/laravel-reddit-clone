<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Community;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CommunityController extends Controller
{
    public function show($slug)
    {
        return Inertia::render('Frontend/Communities/Show', [
            'community' => Community::where('slug', $slug)->first()
        ]);
    }
}
