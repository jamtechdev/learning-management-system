@extends('emails.layout')

@section('title', 'Verify Your Email - QTN Vault')

@section('content')
    <h1>🎉 Welcome to <span class="highlight">QTN Vault</span>! 🎉</h1>
    <p>Hi there! We're super excited to have you join us!</p>
    <p>Before we start the fun, please verify your email address:</p>
    <a href="{{ $verificationUrl }}" class="btn">🔒 Verify My Email</a>
    <div class="footer">
        If you didn’t sign up, no worries – just ignore this email. 😊
    </div>
@endsection
