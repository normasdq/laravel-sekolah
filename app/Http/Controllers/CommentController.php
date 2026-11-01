<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CommentController extends Controller
{
    public function index(Post $post)
    {
        $comments = $post->comments()
            ->approved()
            ->root()
            ->with(['replies' => function ($query) {
                $query->approved()->latest();
            }])
            ->latest()
            ->get();

        return response()->json($comments);
    }

    public function store(Request $request, Post $post)
    {
        $rules = [
            'body' => 'required|string|max:5000',
            'parent_id' => [
                'nullable',
                Rule::exists('comments', 'id')->where('post_id', $post->id),
            ],
            'email' => 'nullable|email|max:255',
        ];

        if (! $request->user()) {
            $rules['name'] = 'required|string|max:100';
        }

        $data = $request->validate($rules);

        // Sanitasi teks agar aman dari tag HTML yang disisipkan
        $data['body'] = strip_tags($data['body']);
        if (isset($data['name'])) {
            $data['name'] = strip_tags($data['name']);
        }

        if ($request->user()) {
            $data['user_id'] = $request->user()->id;
            $data['name'] = $request->user()->name;
            $data['email'] = $request->user()->email;
        }

        $data['post_id'] = $post->id;
        $data['is_approved'] = false;

        $comment = Comment::create($data);

        if ($comment->parent_id) {
            $comment->load('parent.user');
        }

        return response()->json($comment, 201);
    }
}
