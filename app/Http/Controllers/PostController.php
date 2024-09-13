<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function uploadReel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'video_path' => 'required|max:50000',
        ]);
        if ($validator->fails()) 
        {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $videoPath =$request->file('video_path')->getClientOriginalName();
                $request->file('video_path')->move(public_path() . '/reels/', $videoPath);
        $reel = new Post();
        $reel->user_id = auth()->id();
        $reel->video_path = $videoPath;
        $reel->save();

        return response()->json([
            'message' => 'Reel uploaded successfully',
            'reel' => $reel,
        ]);
    }

    public function getReels()
    {
        $reels = Post::join('users','users.id','posts.user_id')->orderBy('posts.id', 'desc')->paginate(10);
        return response()->json([
            'reels' => $reels,
        ]);
    }
    public function playReel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);
        if ($validator->fails()) 
        {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $id=$request->id;
        $reel = Post::findOrFail($id);

        // Increment view count
        $reel->increment('views');

        return response()->json([
            'video_url' => asset('storage/' . $reel->video_path),
            'views' => $reel->views,
        ]);
    }

}
