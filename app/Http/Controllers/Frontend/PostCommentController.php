<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Community;
use App\Models\Post;
use Illuminate\Http\Request;

class PostCommentController extends Controller
{
    public function store(Request $request, Community $community, Post $post)
    {
        $post->comments()->create($request->validate([
            'content' => ['required', 'string', 'min:2']
        ]) + ['user_id' => auth()->id()]);

        return back()->with('message', 'You have created comment successfully');
    }
}
