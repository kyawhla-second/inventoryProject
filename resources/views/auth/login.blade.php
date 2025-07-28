@extends('layouts.auth')

@section('content')
<div class="auth-card">
    <div class="auth-header">
        <div class="auth-logo">
            <i class="fas fa-cube"></i>
        </div>
        <h1 class="auth-title">{{ config('app.name', 'Inventory') }}</h1>
        <p class="auth-subtitle">{{ __('Welcome back! Please sign in to your account') }}</p>
    </div>

    <div class="auth-body">
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">{{ __('Email Address') }}</label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                       name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                       placeholder="{{ __('Enter your email address') }}">
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">{{ __('Password') }}</label>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                       name="password" required autocomplete="current-password"
                       placeholder="{{ __('Enter your password') }}">
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" 
                           {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">
                        {{ __('Remember Me') }}
                    </label>
                </div>
            </div>

            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt me-2"></i>{{ __('Sign In') }}
                </button>
            </div>

            @if (Route::has('password.request'))
                <div class="text-center">
                    <a class="btn btn-link" href="{{ route('password.request') }}">
                        {{ __('Forgot Your Password?') }}
                    </a>
                </div>
            @endif

            @if (Route::has('register'))
                <div class="text-center mt-3">
                    <span class="text-muted">{{ __("Don't have an account?") }}</span>
                    <a href="{{ route('register') }}" class="btn btn-link p-0 ms-1">
                        {{ __('Sign up here') }}
                    </a>
                </div>
            @endif
        </form>
    </div>
</div>
@endsection
