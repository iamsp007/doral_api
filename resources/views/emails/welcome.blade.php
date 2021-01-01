@extends('emails.layouts.app')
@section('title','Welcome Your Name')
@section('content')
    <h1>{{ $user->first_name }} {{ $user->last_name }}</h1>
    <p>your Otp Here is : {{ $otp }}</p>
    <p>Thank you</p>
@endsection
