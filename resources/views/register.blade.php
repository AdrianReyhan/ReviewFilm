@extends('movies_layouts', ['title' => 'Register'])

@section('content')
<main class="main-content">
    <div class="container register">
        <div class="page">
            <div class="breadcrumbs">
                <a href="{{ url('') }}">{{ __("messages.home") }}</a>
                <span>{{__("messages.register") }}</span>
            </div>
            <div class="content">
                @if($errors->any())
                    <div class="alert alert-danger">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </div>
                @endif
                <div class="row">
                    <div class="col-md-6">
                        <h2>{{ __("messages.register") }}</h2>
                        <div class="contact-form">
                            <form action="{{ url('register') }}" method="POST">
                                @csrf
                                <input type="text" name="username" class="name" placeholder="{{ __("messages.username") }}" value="{{ old('username') }}">
                                <input type="email" name="email" class="email" placeholder="{{ __("messages.email") }}" value="{{ old('email') }}">
                                <input type="password" name="password" class="email" placeholder="{{ __("messages.password") }}">
                                <input type="text" name="name" class="name" placeholder="{{ __("messages.name") }}" value="{{ old('name') }}">
                                <button>{{ __("messages.register") }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- .container -->
</main>
@endsection
