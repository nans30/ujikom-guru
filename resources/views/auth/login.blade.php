@extends('layouts.auth')

@section('title', 'Login')
@section('auth-subtitle', 'Sign in to continue to your dashboard.')

@section('content')
    <form action="{{ route('login') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="userEmail" class="form-label">Email address <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="ti ti-mail text-muted fs-xl"></i></span>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="userEmail" name="email"
                    value="{{ old('email') }}" placeholder="you@example.com" required autocomplete="email">
            </div>
            @error('email')
                <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="userPassword" class="form-label">Password <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="ti ti-lock-password text-muted fs-xl"></i></span>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="userPassword"
                    name="password" placeholder="••••••••" required autocomplete="current-password">
            </div>
            @error('password')
                <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="form-check">
                <input class="form-check-input form-check-input-light fs-14" type="checkbox" id="remember" name="remember"
                    {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember">Keep me signed in</label>
            </div>
            <a href="{{ route('password.request') }}" class="text-decoration-underline link-offset-3 text-muted">Forgot
                Password?</a>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary fw-semibold py-2">Sign In</button>
        </div>
    </form>

    <p class="text-muted text-center mt-4 mb-0">
        New here? <a href="{{ route('register') }}" class="text-decoration-underline link-offset-3 fw-semibold">Create an
            account</a>
    </p>
@endsection
