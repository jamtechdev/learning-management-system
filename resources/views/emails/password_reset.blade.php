@extends('emails.layout')

@section('title', 'Password Reset OTP')

@section('content')
    <h2>Password Reset OTP</h2>
    <p>Your OTP for password reset is: <strong>{{ $otp }}</strong></p>
    <p>This OTP will expire in 10 minutes.</p>
@endsection
