@extends('layout.parent')

@section('title', '404 Error')

@section('main')

    <section class="section error-404 min-vh-100 d-flex flex-column align-items-center justify-content-center">
        <h1>500</h1>
        <h2>Oops, you ran into a server error, please contact system administrator.</h2>
        <a class="btn" href="{{route('dashboard')}}">Back to home</a>
        <img src="{{ asset('img/not-found.svg') }}" class="img-fluid py-5" alt="Page Not Found">
    </section>

@endsection

