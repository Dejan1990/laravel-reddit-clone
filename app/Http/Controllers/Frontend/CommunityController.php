<?php

namespace App\Http\Controllers\Frontend;

use Inertia\Inertia;
use App\Models\Community;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CommunityPostResource;

class CommunityController extends Controller
{
    public function show($slug)
    {
        $community = Community::where('slug', $slug)->first();
        $posts = CommunityPostResource::collection($community->posts()->with('user')->paginate(3));

        return Inertia::render('Frontend/Communities/Show', [
            'community' => $community,
            'posts' => $posts
        ]);
    }
}
