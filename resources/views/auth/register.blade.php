@extends('layouts.app')

@section('title', 'Sign Up - Cinema Bunny')

@section('content')
<div style="min-height: 80vh; display: flex; align-items: center; justify-content: center; padding: 50px;">
    <div style="background: rgba(255, 255, 255, 0.05); padding: 40px; border-radius: 8px; max-width: 400px; width: 100%;">
        <h1 style="font-size: 32px; margin-bottom: 30px; text-align: center;">Sign Up</h1>
        
        <form method="POST" action="{{ route('register') }}">
            @csrf
            
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-bottom: 20px;">Sign Up</button>
        </form>

        <p style="text-align: center; color: rgba(255, 255, 255, 0.7);">
            Already have an account? <a href="{{ route('login') }}" style="color: #e50914;">Login</a>
        </p>
    </div>
</div>
@endsection

