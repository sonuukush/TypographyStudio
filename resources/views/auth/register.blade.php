@extends('layouts.app')

@section('title', 'Register — Typography Studio')
@section('description', 'Create a free Typography Studio account to generate stunning animated typography videos.')

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

        <h1 class="auth-title">Create your account</h1>
        <p class="auth-subtitle">Free forever — no credit card required</p>

        <form method="POST" action="{{ route('register') }}" style="display:flex;flex-direction:column;gap:1.1rem;">
            @csrf

            {{-- Name --}}
            <div>
                <label class="form-label" for="name">Full Name</label>
                <input
                    id="name"
                    class="form-input"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="Your Name"
                >
                @error('name')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

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
                    autocomplete="username"
                    placeholder="you@example.com"
                >
                @error('email')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div>
                <label class="form-label" for="password">Password</label>
                <input
                    id="password"
                    class="form-input"
                    type="password"
                    name="password"
                    required
                    autocomplete="new-password"
                    placeholder="Min. 8 characters"
                >
                @error('password')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Confirm Password --}}
            <div>
                <label class="form-label" for="password_confirmation">Confirm Password</label>
                <input
                    id="password_confirmation"
                    class="form-input"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="Repeat password"
                >
                @error('password_confirmation')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="btn-primary w-full" style="justify-content:center;padding:0.85rem;font-size:0.95rem;margin-top:0.25rem;">
                Create Free Account
            </button>
        </form>

        <hr class="auth-divider">

        <p style="text-align:center;font-size:0.85rem;color:rgba(255,255,255,0.4);">
            Already have an account?
            <a href="{{ route('login') }}" class="auth-footer-link">Sign in</a>
        </p>
    </div>
</div>
@endsection
