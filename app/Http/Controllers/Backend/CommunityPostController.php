<?php

namespace App\Http\Controllers\Backend;

use App\Models\Post;
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

    public function edit(Community $community, Post $post)
    {
        $this->authorize('update', $post);

        return Inertia::render('Communities/Post/Edit', [
            'community' => $community,
            'post' => $post
        ]);
    }

    public function update(StorePostRequest $request, Community $community, Post $post)
    {
        $this->authorize('update', $post);

        $post->update($request->validated());

        return to_route('frontend.communities.posts.show', [$community->slug, $post->slug])
            ->with('message', 'Post updated successfully');
    }

    public function destroy(Community $community, Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return to_route('frontend.community.show', $community->slug)
            ->with('message', 'Post deleted successfully');
    }
}
