<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $posts = Post::search(request()->get('term'))->latest()->get();
        $posts->map(function (Post $post) {
            $post->tags = json_decode($post->tags);
            return $post;
        });

        return response()->json($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z0-9 .,\-]+$/', 'unique:posts,title'],
            'content' => ['required', 'string', 'max:5000',],
            'category' => ['required', 'string', 'max:50'],
            'tags' => ['required', 'array'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $validated = $validator->validated();

        $validated['tags'] = json_encode($validated['tags']);
        $post = Post::create($validated);
        $post['tags'] = json_decode($post['tags']);

        return response()->json($post, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post): JsonResponse
    {
        $post->tags = json_decode($post->tags);

        return response()->json($post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z0-9 .,\-]+$/', Rule::unique('posts', 'title')->ignore($post->id, 'id')],
            'content' => ['required', 'string', 'max:5000',],
            'category' => ['required', 'string', 'max:50'],
            'tags' => ['required', 'array'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $validated = $validator->validated();

        $post->update($validated);

        return response()->json($post);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post): JsonResponse
    {
        $post->delete();

        return response()->json(status: 204);
    }
}
