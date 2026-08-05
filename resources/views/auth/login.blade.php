@extends('layouts.app')

@section('title', 'Login — Typography Studio')
@section('description', 'Login to Typography Studio to create and download stunning animated typography videos.')

@section('content')
<div class="auth-page">
    <div class="auth-card">
        {{-- Logo --}}
        <div class="flex items-center gap-2 mb-8">
            <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#7c3aed,#ec4899);display:flex;align-items:center;justify-content:center;">
                <svg width="16" height="16" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h10M4 18h6"/>
                </svg>
            </div>
            <span style="font-family:'Fraunces',serif;font-weight:800;font-size:1.1rem;color:#fff;">Typography<span class="text-gradient">Studio</span></span>
        </div>

        <h1 class="auth-title">Welcome back</h1>
        <p class="auth-subtitle">Sign in to your account to continue creating</p>

        {{-- Session Status --}}
        @if (session('status'))
            <div style="background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.2);color:#4ade80;padding:0.75rem 1rem;border-radius:10px;font-size:0.85rem;margin-bottom:1.25rem;">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" style="display:flex;flex-direction:column;gap:1.25rem;">
            @csrf

            {{-- Email --}}
            <div>
                <label class="form-label" for="email">Email Address</label>
                <input
                    id="email"
                    class="form-input"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="you@example.com"
                >
                @error('email')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.4rem;">
                    <label class="form-label" for="password" style="margin-bottom:0;">Password</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" style="font-size:0.78rem;color:#a78bfa;text-decoration:none;">Forgot password?</a>
                    @endif
                </div>
                <input
                    id="password"
                    class="form-input"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                >
                @error('password')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remember me --}}
            <div style="display:flex;align-items:center;gap:0.5rem;">
                <input type="checkbox" id="remember_me" name="remember" style="accent-color:#7c3aed;width:15px;height:15px;">
                <label for="remember_me" style="font-size:0.82rem;color:rgba(255,255,255,0.5);cursor:pointer;">Remember me</label>
            </div>

            <button type="submit" class="btn-primary w-full" style="justify-content:center;padding:0.85rem;font-size:0.95rem;">
                Sign In
            </button>
        </form>

        <hr class="auth-divider">

        <p style="text-align:center;font-size:0.85rem;color:rgba(255,255,255,0.4);">
            Don't have an account?
            <a href="{{ route('register') }}" class="auth-footer-link">Create one free</a>
        </p>
    </div>
</div>
@endsection
