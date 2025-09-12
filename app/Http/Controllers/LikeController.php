<?php

namespace App\Http\Controllers;

use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LikeController extends Controller
{
    public function toggle(Request $request)
    {
        $request->validate([
            'likeable_type' => 'required|string',
            'likeable_id' => 'required|string',
        ]);

        // FIXED: Handle all the different ways likeable_type might be sent
        $typeMap = [
            'App\Models\Image' => 'App\Models\Image',
            'App\\Models\\Image' => 'App\Models\Image',
            'image' => 'App\Models\Image',
            'Image' => 'App\Models\Image',
            
            'App\Models\Comment' => 'App\Models\Comment',
            'App\\Models\\Comment' => 'App\Models\Comment',
            'comment' => 'App\Models\Comment',
            'Comment' => 'App\Models\Comment',
            
            'App\Models\Album' => 'App\Models\Album',
            'App\\Models\\Album' => 'App\Models\Album',
            'album' => 'App\Models\Album',
            'Album' => 'App\Models\Album',
        ];

        $likeableType = $typeMap[$request->likeable_type] ?? null;

        if (!$likeableType) {
            \Log::error('Invalid likeable type: ' . $request->likeable_type);
            return response()->json(['error' => 'Invalid likeable type'], 422);
        }

        $likeableModel = $likeableType::find($request->likeable_id);
        if (!$likeableModel) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        $user = auth()->user();
        $existingLike = Like::where([
            'user_id' => $user->id,
            'likeable_type' => $likeableType,
            'likeable_id' => $request->likeable_id,
        ])->first();

        DB::beginTransaction();
        try {
            if ($existingLike) {
                $existingLike->delete();
                $likeableModel->decrement('likes_count');
                $liked = false;
            } else {
                Like::create([
                    'user_id' => $user->id,
                    'likeable_type' => $likeableType,
                    'likeable_id' => $request->likeable_id,
                    'user_ip' => $request->ip(),
                ]);
                $likeableModel->increment('likes_count');
                $liked = true;
            }

            DB::commit();

            return response()->json([
                'liked' => $liked,
                'likes_count' => $likeableModel->fresh()->likes_count ?? 0,
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Like toggle error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to toggle like'], 500);
        }
    }
}
