@extends('emails.layout')

@section('content')

<p>Hello <strong>{{ $user->name }}</strong>,</p>

<p>We received a request to reset your password. Click the button below to set a new one:</p>

<a href="{{ $resetUrl }}" 
   style="
        display:inline-block;
        background-color:#007bff;
        color:#ffffff !important;
        padding:12px 22px;
        text-decoration:none;
        border-radius:6px;
        font-weight:600;
        font-size:15px;
        text-align:center;
   ">
    Reset Password
</a>


<p>This link will expire in 60 minutes. If you did not request a password reset, you can safely ignore this email.</p>

<p>Thanks,<br>

<!-- {{ config('app.name') }}  -->
Team</p>

@endsection

