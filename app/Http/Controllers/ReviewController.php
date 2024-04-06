<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\User;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'movie_id' => 'required',
            'title' => 'required',
            'poster' => 'required',
            'year' => 'required',
            'rating' => 'required|integer|min:1|max:10',
            'comment' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $token = str_replace('Bearer ', '', $request->header('Authorization'));
        $user = User::where('token', $token)->first();

        if ($user) {
            $review = new Review();
            $review->movie_id = $request->input('movie_id');
            $review->title = $request->input('title');
            $review->poster = $request->input('poster');
            $review->year = $request->input('year');
            $review->user_id = $user->id;
            $review->user_name = $user->username;
            $review->rating = $request->input('rating');
            $review->comment = $request->input('comment');
            $review->save();

            return response()->json(['message' => 'Review stored successfully'], 201);
        } else {
            // User is not authenticated, handle the error accordingly
            return response()->json(['message' => 'User not authenticated'], 401);
        }
    }
}
