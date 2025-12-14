@extends('layouts.app')

@section('title', 'Login - Cinema Bunny')

@section('content')
<div style="min-height: 80vh; display: flex; align-items: center; justify-content: center; padding: 50px;">
    <div style="background: rgba(255, 255, 255, 0.05); padding: 40px; border-radius: 8px; max-width: 400px; width: 100%;">
        <h1 style="font-size: 32px; margin-bottom: 30px; text-align: center;">Login</h1>
        
        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
                <input type="checkbox" id="remember" name="remember" style="width: auto;">
                <label for="remember" style="margin: 0;">Remember me</label>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-bottom: 20px;">Login</button>
        </form>

        <p style="text-align: center; color: rgba(255, 255, 255, 0.7);">
            Don't have an account? <a href="{{ route('register') }}" style="color: #e50914;">Sign up</a>
        </p>
    </div>
</div>
@endsection

