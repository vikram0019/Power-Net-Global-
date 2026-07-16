@extends('layouts.app')

@section('title', 'Contact Us — PowerNetGlobal')

@section('content')
    <section class="hero-png py-5">
        <div class="container py-5 text-center">
            <div class="eyebrow-gold mb-2">Contact Us</div>
            <h1 class="display-6 fw-bold">We'd love to hear from you</h1>
        </div>
    </section>

    <section class="py-5">
        <div class="container py-4">
            <div class="row g-5">
                <div class="col-lg-5">
                    <h5 class="fw-bold mb-4">Get in Touch</h5>
                    <div class="d-flex gap-3 mb-4">
                        <i class="bi bi-envelope fs-4" style="color: var(--png-gold-500);"></i>
                        <div>
                            <div class="fw-semibold">Email</div>
                            <div class="text-muted small">support@powernetglobal.com</div>
                        </div>
                    </div>
                    <div class="d-flex gap-3 mb-4">
                        <i class="bi bi-telephone fs-4" style="color: var(--png-gold-500);"></i>
                        <div>
                            <div class="fw-semibold">Phone</div>
                            <div class="text-muted small">+1 (000) 000-0000</div>
                        </div>
                    </div>
                    <div class="d-flex gap-3 mb-4">
                        <i class="bi bi-geo-alt fs-4" style="color: var(--png-gold-500);"></i>
                        <div>
                            <div class="fw-semibold">Address</div>
                            <div class="text-muted small">Global Business Center</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card-png p-4">
                        @if (session('status'))
                            <div class="alert alert-success">{{ session('status') }}</div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form method="POST" action="{{ route('contact.submit') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-semibold">Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-semibold">Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Message</label>
                                <textarea name="message" rows="5" class="form-control" required>{{ old('message') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-gold fw-bold px-4">Send Message</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
