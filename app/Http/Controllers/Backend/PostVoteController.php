<?php

namespace App\Http\Controllers\Backend;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PostVote;

class PostVoteController extends Controller
{
    public function upVote(Post $post)
    {
        $isVoted = PostVote::query()
            ->where('post_id', $post->id)
            ->where('user_id', auth()->id())
            ->first();

        if (!is_null($isVoted)) {
            if ($isVoted->vote === -1) {
                $isVoted->update(['vote' => 1]);
                $post->increment('votes', 2);
                return back();
            } elseif ($isVoted->vote === 1) {
                return back();
            }
        } else {
            PostVote::create([
                'post_id' => $post->id,
                'user_id' => auth()->id(),
                'vote' => 1
            ]);

            $post->increment('votes', 1);
            return back();
        }
    }

    public function downVote(Post $post)
    {
        $isVoted = PostVote::query()
            ->where('post_id', $post->id)
            ->where('user_id', auth()->id())
            ->first();

        if (!is_null($isVoted)) {
            if ($isVoted->vote === 1) {
                $isVoted->update(['vote' => -1]);
                $post->decrement('votes', 2);
                return back();
            } elseif ($isVoted->vote === -1) {
                return back();
            }
        } else {
            PostVote::create([
                'post_id' => $post->id,
                'user_id' => auth()->id(),
                'vote' => -1
            ]);

            $post->decrement('votes', 1);
            return back();
        }
    }
}
