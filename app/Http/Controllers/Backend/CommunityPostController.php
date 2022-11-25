<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Community;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CommunityPostController extends Controller
{
    public function create(Community $community)
    {
        return Inertia::render('Communities/Post/Create', [
            'community' => $community
        ]);
    }
}
