@extends('front.frontLayout.master')
@section('content')
<div class="container">

    <section class="section error-404 min-vh-100 d-flex flex-column align-items-center justify-content-center">
        <h1>{{ $status }}</h1>
        @if ($message)
        <h2>{{ $message }}</h2>
        @else
        <h2>The page you are looking for doesn't exist.</h2>
        @endif

        <a class="btn" href="{{ URL::to('/') }}">Back to home</a>
        <img src="{{ asset('assets/not-found.svg') }}" class="img-fluid py-5" alt="Page Not Found" style="width: 50%;">
    </section>

</div>
@endsection
