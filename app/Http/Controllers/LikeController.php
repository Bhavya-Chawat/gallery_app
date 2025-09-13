<?php

namespace App\Http\Controllers;

use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            Log::error('Invalid likeable type: ' . $request->likeable_type);
            return response()->json(['error' => 'Invalid likeable type'], 422);
        }

        // FIXED: Check if model exists and user is authenticated
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $likeableModel = $likeableType::find($request->likeable_id);
        if (!$likeableModel) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        // FIXED: Use database transaction with proper locking
        return DB::transaction(function () use ($user, $likeableType, $request, $likeableModel) {
            // FIXED: Use lockForUpdate to prevent race conditions
            $existingLike = Like::where([
                'user_id' => $user->id,
                'likeable_type' => $likeableType,
                'likeable_id' => $request->likeable_id,
            ])->lockForUpdate()->first();

            try {
                if ($existingLike) {
                    // Unlike: Remove the like
                    $existingLike->delete();
                    
                    // FIXED: Use lockForUpdate on the model to prevent race conditions
                    $likeableModel->lockForUpdate();
                    $likeableModel->decrement('likes_count');
                    $liked = false;
                    
                } else {
                    // Like: Create new like with additional safeguards
                    try {
                        Like::create([
                            'user_id' => $user->id,
                            'likeable_type' => $likeableType,
                            'likeable_id' => $request->likeable_id,
                            'user_ip' => $request->ip(),
                        ]);
                        
                        // FIXED: Use lockForUpdate to prevent race conditions
                        $likeableModel->lockForUpdate();
                        $likeableModel->increment('likes_count');
                        $liked = true;
                        
                    } catch (\Illuminate\Database\QueryException $e) {
                        // FIXED: Handle duplicate key constraint violations
                        if ($e->errorInfo[1] == 1062) { // MySQL duplicate entry error
                            // Like already exists, treat as already liked
                            Log::warning('Duplicate like attempt prevented', [
                                'user_id' => $user->id,
                                'likeable_type' => $likeableType,
                                'likeable_id' => $request->likeable_id,
                            ]);
                            
                            $liked = true;
                            // Don't increment count since like already exists
                        } else {
                            throw $e; // Re-throw if it's a different error
                        }
                    }
                }

                // FIXED: Get fresh count from database to ensure accuracy
                $freshModel = $likeableType::find($request->likeable_id);
                $actualLikesCount = $freshModel ? $freshModel->likes_count : 0;

                return response()->json([
                    'liked' => $liked,
                    'likes_count' => $actualLikesCount,
                    'success' => true
                ]);

            } catch (\Exception $e) {
                Log::error('Like toggle error: ' . $e->getMessage(), [
                    'user_id' => $user->id,
                    'likeable_type' => $likeableType,
                    'likeable_id' => $request->likeable_id,
                    'trace' => $e->getTraceAsString()
                ]);
                
                throw $e; // Let transaction handle the rollback
            }
        });
    }
}
