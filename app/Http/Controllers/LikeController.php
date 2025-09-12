<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Like;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function toggle(Request $request)
    {
        $request->validate([
            'likeable_type' => 'required|string',
            'likeable_id' => 'required|string',
        ]);

        $likeable = $this->getLikeable($request->likeable_type, $request->likeable_id);
        
        $existingLike = Like::where([
            'likeable_type' => $request->likeable_type,
            'likeable_id' => $request->likeable_id,
            'user_id' => auth()->id(),
        ])->first();

        if ($existingLike) {
            $existingLike->delete();
            $liked = false;
        } else {
            Like::create([
                'likeable_type' => $request->likeable_type,
                'likeable_id' => $request->likeable_id,
                'user_id' => auth()->id(),
                'user_ip' => $request->ip(),
            ]);
            $liked = true;
        }

        return response()->json([
            'liked' => $liked,
            'likes_count' => $likeable->likes()->count(),
        ]);
    }

    private function getLikeable($type, $id)
    {
        switch ($type) {
            case 'App\\Models\\Image':
                return Image::findOrFail($id);
            default:
                abort(422, 'Invalid likeable type');
        }
    }
}
