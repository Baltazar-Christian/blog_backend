<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use Illuminate\Http\Request;

class VenueController extends Controller
{
    // get all venues
    public function index()
    {
        return response([
            'venues' => Venue::orderBy('created_at', 'desc')
            ->get()
        ], 200);
    }

    // get single post
    public function show($id)
    {
        return response([
            'post' => Venue::where('id', $id)->get()
        ], 200);
    }

    // create a post
    public function store(Request $request)
    {
        //validate fields
        $attrs = $request->validate([
            'body' => 'required|string'
        ]);

        $image = $this->saveImage($request->image, 'venues');

        $post = Venue::create([
            'body' => $attrs['body'],
            'user_id' => auth()->user()->id,
            'image' => $image
        ]);

        // for now skip for post image

        return response([
            'message' => 'Venue created.',
            'post' => $post,
        ], 200);
    }

    // update a post
    public function update(Request $request, $id)
    {
        $post = Venue::find($id);

        if(!$post)
        {
            return response([
                'message' => 'Venue not found.'
            ], 403);
        }

        if($post->user_id != auth()->user()->id)
        {
            return response([
                'message' => 'Permission denied.'
            ], 403);
        }

        //validate fields
        $attrs = $request->validate([
            'body' => 'required|string'
        ]);

        $post->update([
            'body' =>  $attrs['body']
        ]);

        // for now skip for post image

        return response([
            'message' => 'Venue updated.',
            'post' => $post
        ], 200);
    }

    //delete post
    public function destroy($id)
    {
        $post = Venue::find($id);

        if(!$post)
        {
            return response([
                'message' => 'Venue not found.'
            ], 403);
        }

        if($post->user_id != auth()->user()->id)
        {
            return response([
                'message' => 'Permission denied.'
            ], 403);
        }

        $post->comments()->delete();
        $post->likes()->delete();
        $post->delete();

        return response([
            'message' => 'Venue deleted.'
        ], 200);
}
}