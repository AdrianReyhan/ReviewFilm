<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware('Language')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin']);
    Route::post('/login', [AuthController::class, 'loginProcess']);

    Route::middleware(['MustLogin'])->group(function () {
        Route::get('/movies', [MovieController::class, 'index']);
        Route::get('/movies/{id}', [MovieController::class, 'detailMovies']);
        Route::post('movies/delete/{imdb_id}', [MovieController::class, 'deleteFavMovies']);
        Route::get('/favorit_movies', [MovieController::class, 'favMovies']);
        Route::get('/review_movies', [MovieController::class, 'reviewMovies'])->name('review.movies');
        Route::post('/movies/add', [MovieController::class, 'addToFavMovies']);
        Route::get('/logout', [AuthController::class, 'logoutProcess']);
        Route::post('/reviews/store', [ReviewController::class, 'store'])->name('store.review');
    });

    // Registration routes
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'registerProcess']);

    // Language change route
    Route::get('/language/{langcode}', [AuthController::class, 'changeLanguage']);
});

