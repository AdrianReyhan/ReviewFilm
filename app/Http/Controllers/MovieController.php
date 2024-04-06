<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Review;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class MovieController extends Controller
{
    //
    public function index()
    {
        return view('movies');
    }

    public function listMovies(Request $request)
    {
        $client = new Client();
        $query = [
            'apikey'    => '6d7e17ab',
            's'         => $request->title,
        ];
        if($request->type != ""){
            $query['type'] = $request->type;
        }
        if($request->year != ""){
            $query['y'] = $request->year;
        }
        if($request->page !== ""){
            $query['page'] = $request->page;
        }
        $request = $client->request('GET','https://www.omdbapi.com/', [
            'query' => $query
        ]);
        $response = json_decode($request->getBody());
        $data = [
            'data'      => [],
            'response'  => false,
            'message'   => ''
        ];
        if($response->Response == "True"){
            for($i=0; $i < count($response->Search); $i++){
                $newData = [
                    'imdbID'        => $response->Search[$i]->imdbID,
                    'title'         => $response->Search[$i]->Title,
                    'year'          => $response->Search[$i]->Year,
                    'poster'        => '<img data-src="'.$response->Search[$i]->Poster.'" class="lozad fade">',
                ];
                array_push($data['data'], $newData);
            }
            $data['response'] = true;
        }else{
            $data['message']  = __('messages.movie_notfound');
        }
        return response()->json($data);
    }

    public function detailMovies($imdbId, $jsonNeeded = false)
    {
        $client = new Client();
        $query = [
            'apikey'    => '6d7e17ab',
            'i'         => $imdbId
        ];
        $request = $client->request('GET','https://www.omdbapi.com/', [
            'query' => $query
        ]);
        $response = $request->getBody();
        $data = json_decode($response);
        if($jsonNeeded == false){
            if($data->Response == "True"){
                return view('movies_detail',[
                    'movieDetails'  => $data,
                ]);
            }else{
                return redirect()->to(url(''));
            }
        }else{
            return $response;
        }
    }

    public function addFavMovies(Request $request)
    {
        $imdb_id = $request->imdb_id;
        $token = str_replace('Bearer ','', $request->header('Authorization'));
        $user = User::where('token', $token);
        if($user->count() > 0){
            $user = $user->first();
            $filterFav = DB::table('favorit_movies')->where('imdb_id', $imdb_id)->where('user_id', $user->id);
            if($filterFav->count() > 0){
                $filterFav->delete();
            }
            $data = json_decode($this->detailMovies($imdb_id, true));
            if($data->Response == "True"){
                $insert = [
                    'imdb_id'   => $imdb_id,
                    'user_id'   => $user->id,
                    'title'     => $data->Title,
                    'plot'      => $data->Plot,
                    'poster'    => $data->Poster,
                    'year'      => $data->Year,
                    'rating'    => $data->imdbRating,
                    'length'    => $data->Runtime,
                    'released'  => $data->Released,
                    'genre'     => $data->Genre,
                    'director'  => $data->Director,
                    'writer'    => $data->Writer,
                    'actor'     => $data->Actors,
                    'created_at'=> \Carbon\Carbon::now(),
                    'updated_at'=> \Carbon\Carbon::now()
                ];
                DB::table('favorit_movies')->insert($insert);
                $is_success =  true;
                $message = $data->Title. " has been added to favorite list";
            }else{
                $is_success = false;
                $message = "Movie not found";
            }
        }else{
            $is_success =  false;
            $message = "User not found";
        }
        return response()->json([
            'is_success'    => $is_success,
            'message'       => $message
        ]);
    }

    public function favMovies()
    {
        $favMovies = DB::table('favorit_movies')->where('user_id', session('id_user'));
        return view('favorit_movies', ['favMovies' => $favMovies]);
    }

    public function deleteFavMovies($imdb_id)
    {
        $data = DB::table('favorit_movies')
        ->where('imdb_id', $imdb_id)
        ->where('user_id', session('id_user'));
        if($data->count() > 0){
            $data->delete();
            return redirect()->back()->with('success', __("messages.fav_movie_success_deleted"));
        }else{
            return redirect()->back()->withErrors([__("messages.fav_movie_fail_deleted")]);
        }
    }

    public function storeReview(Request $request)
    {
        $validatedData = $request->validate([
            'movie_id' => 'required',
            'rating' => 'required|integer|min:1|max:10',
            'comment' => 'required',
        ]);

        if (Auth::check()) {
            $user = Auth::user();

            $review = new Review();
            $review->movie_id = $validatedData['movie_id'];
            $review->user_id = $user->id;
            $review->user_name = $user->username;
            $review->rating = $validatedData['rating'];
            $review->comment = $validatedData['comment'];
            $review->save();

            return response()->json(['message' => 'Review stored successfully'], 201);
        } else {
            // User is not authenticated, handle the error accordingly
            return response()->json(['message' => 'User not authenticated'], 401);
        }
    }

    public function reviewMovies()
    {
        $reviewMovies = DB::table('reviews')->where('user_id', session('id_user'));
        return view('review_movies', ['reviewMovies' => $reviewMovies]);
    }
}
