<?php

namespace App\Http\Controllers\Backend;

use Inertia\Inertia;
use App\Models\Community;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;

class CommunityPostController extends Controller
{
    public function create(Community $community)
    {
        return Inertia::render('Communities/Post/Create', [
            'community' => $community
        ]);
    }

    public function store(StorePostRequest $request, Community $community)
    {
        $community->posts()->create($request->validated() + ['user_id' => auth()->id()]);

        return to_route('frontend.community.show', $community->slug)
            ->with('message', 'Post created successfully');
    }
}
