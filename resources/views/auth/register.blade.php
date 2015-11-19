@extends('layouts.master')

@section('title', 'hoard')

@section('content')

<div id="inline-container">
	<form method="POST" action="{!! action('Auth\AuthController@postRegister') !!}">
	    {!! csrf_field() !!}
	
	    <div>
	        Name
	        <input type="text" name="name" value="{{ old('name') }}">
	    </div>
	
	    <div>
	        Email
	        <input type="email" name="email" value="{{ old('email') }}">
	    </div>
	
	    <div>
	        Password
	        <input type="password" name="password">
	    </div>
	
	    <div>
	        Confirm Password
	        <input type="password" name="password_confirmation">
	    </div>
	
	    <div>
	        <button type="submit">Register</button>
	    </div>
	</form>
</div>
@endsection