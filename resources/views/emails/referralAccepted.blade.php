@extends('emails.layouts.app')
@section('title','Welcome To Doral Health Connect ')
@section('content')

    <h1>Hi, {{ $details['name'] }}</h1>
    <p> Thank you for register with us. Please Activate Your Account and Login With default Password<br/>
        <h2> Default Password : {{ $details['password'] }}</h2></br>
        <a href="{{ $details['href'] }}">Activate Account</a>
    </p>
    <p>Thank you</p>
@endsection
