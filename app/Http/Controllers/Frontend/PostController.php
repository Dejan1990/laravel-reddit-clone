<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostShowResource;
use App\Models\Community;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PostController extends Controller
{
    public function show(Community $community, Post $post)
    {
        return Inertia::render('Frontend/Posts/Show', [
            'community' => $community,
            'post' => new PostShowResource($post)
        ]);
    }
}
