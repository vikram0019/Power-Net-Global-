@extends('layouts.app')

@section('title', 'Something Went Wrong — PowerNetGlobal')

@section('content')
    <section class="hero-png py-5">
        <div class="container py-5 text-center">
            <div class="eyebrow-gold mb-2">Error</div>
            <h1 class="display-6 fw-bold">Something went wrong on our end</h1>
            <p class="opacity-75 mt-2">This has been logged and our team will look into it. Please try again shortly.</p>
            <div class="d-flex gap-3 flex-wrap justify-content-center mt-4">
                <a href="{{ route('home') }}" class="btn btn-gold btn-lg px-4 fw-bold">Back to Home</a>
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-4">Login</a>
            </div>
        </div>
    </section>
@endsection
