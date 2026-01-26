@extends('layouts.auth')

@section('title', 'Login')
@section('auth-subtitle', 'Let’s get you signed in. Enter your email and password to continue.')

@section('content')
    <form action="{{ route('register') }}" method="POST">
        @csrf

        <div class="row">
            <!-- First Name -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="firstName" class="form-label">First Name <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="ti ti-user text-muted fs-xl"></i></span>
                        <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="firstName"
                            name="first_name" placeholder="John" value="{{ old('first_name') }}" required>
                    </div>
                    @error('first_name')
                        <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
            </div>

            <!-- Last Name -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="lastName" class="form-label">Last Name <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="ti ti-user text-muted fs-xl"></i></span>
                        <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="lastName"
                            name="last_name" placeholder="Doe" value="{{ old('last_name') }}" required>
                    </div>
                    @error('last_name')
                        <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
            </div>
        </div>


        <!-- Email -->
        <div class="mb-3">
            <label for="userEmail" class="form-label">Email address <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="ti ti-mail text-muted fs-xl"></i></span>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="userEmail"
                    name="email" placeholder="you@example.com" value="{{ old('email') }}" required autocomplete="email">
            </div>
            @error('email')
                <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-3" data-password="bar">
            <label for="userPassword" class="form-label">Password <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="ti ti-lock-password text-muted fs-xl"></i></span>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="userPassword"
                    name="password" placeholder="••••••••" required autocomplete="new-password">
            </div>
            <div class="password-bar my-2"></div>
            <p class="text-muted fs-xs mb-0">Use 8+ characters with letters, numbers & symbols.</p>
            @error('password')
                <span class="text-danger d-block"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm Password <span
                    class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="ti ti-lock-check text-muted fs-xl"></i></span>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                    placeholder="••••••••" required autocomplete="new-password">
            </div>
        </div>

        <!-- Terms & Policy -->
        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input form-check-input-light fs-14" type="checkbox" id="termAndPolicy" required>
                <label class="form-check-label" for="termAndPolicy">Agree the Terms & Policy</label>
            </div>
        </div>

        <!-- Submit -->
        <div class="d-grid">
            <button type="submit" class="btn btn-primary fw-semibold py-2">Create Account</button>
        </div>
    </form>


    <p class="text-muted text-center mt-4 mb-0">
        Already have an account? <a href="{{ route('login') }}" class="text-decoration-underline link-offset-3 fw-semibold">Login</a>
    </p>
@endsection
