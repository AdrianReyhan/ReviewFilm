@extends('movies_layouts', ['title' => 'Add Review'])

@section('content')
<main class="main-content">
    <div class="container">
        <div class="page">
            <div class="breadcrumbs">
                <a href="{{ url('') }}">{{ __("messages.home") }}</a>
                <span>{{ __("messages.add_review") }}</span>
            </div>
            <div class="content">
                <div class="row">
                    <div class="col-md-6 offset-md-3">
                        <form action="{{ route('store.review') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="movie_id">{{ __("messages.movie") }}</label>
                                <select name="movie_id" id="movie_id" class="form-control">
                                    @foreach($movies as $movie)
                                        <option value="{{ $movie->id }}">{{ $movie->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="rating">{{ __("messages.rating") }}</label>
                                <input type="number" name="rating" id="rating" class="form-control" min="1" max="10" required>
                            </div>
                            <div class="form-group">
                                <label for="comment">{{ __("messages.comment") }}</label>
                                <textarea name="comment" id="comment" class="form-control" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">{{ __("messages.submit") }}</button>
                        </form>
                    </div>
                </div> <!-- .row -->
            </div>
        </div>
    </div> <!-- .container -->
</main>
@endsection
