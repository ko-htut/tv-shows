@extends('layouts.main')


@section('meta_title', 'Seriálovna.cz :: Password reset')
@section('meta_description', 'Seriálovna.cz :: Password reset')
@section('page_title', 'Password reset')

@section('content')
<div class="container login valign-wrapper">
    <div class="row">
        <div class="col l12 s12 m12">


            @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="valign-wrapper">
                {{ csrf_field() }}


                <div class="row">

                    <div class="input-field col s12 {{ $errors->has('email') ? ' invalid' : '' }}">

                        <input id="email" placeholder="Email address" type="email" name="email" class="validate" value="{{ old('email') }}" required>
                        <label for="email">Email</label>
                    </div>


                    <div class="col s12 center-align">
                        <button type="submit" class="btn login-submit blue darken-3">
                            Send Password Reset Link
                        </button>
                    </div>

                    <div class="col s12">
                        @if ($errors->has('email'))
                        <span>
                            <strong class="red">{{ $errors->first('email') }}</strong>
                        </span>
                        @endif
                    </div>


            </form>
            
            <div class="col s12 center-align">
                <a  href="{{ route('register') }}">
                    Create an account
                </a>
            </div>
            
             <div class="col s12 center-align">
                <a  href="{{ route('login') }}">
                    Log in
                </a>
            </div>

        </div>
    </div>
</div>
</div>
@endsection

