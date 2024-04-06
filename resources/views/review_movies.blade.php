@extends('movies_layouts',['title' => 'Reviewed Movies'])

@section('content')
<main class="main-content">
    <div class="container">
        <div class="page">
            <div class="breadcrumbs">
                <a href="{{ url('') }}">{{ __("messages.home") }}</a>
                <span>{{ __("messages.review_movie") }}</span>
            </div>
            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </div>
            @endif

            @if(session()->has('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <div class="movie-list">
                @if($reviewMovies->count() > 0)
                    @foreach($reviewMovies->get() as $review)
                        <div class="movie">
                            <figure class="movie-poster"><img src="{{ $review->poster }}" alt="#"></figure>
                            <div class="movie-title"><a href="{{ url('movies/'.$review->movie_id) }}">{{ $review->title }}</a></div>
                            <div class="movie-year">{{ $review->year }}</div>
                            <div class="movie-rating">Rating: {{ $review->rating }}/10</div>
                            <div class="movie-comment">{{ $review->comment }}</div>
                        </div>
                    @endforeach
                @else
                    <p style="text-align:center">{{ __("messages.review_movie_notfound") }}</p>
                @endif
            </div> <!-- .movie-list -->
        </div>
    </div> <!-- .container -->
</main>
@endsection
