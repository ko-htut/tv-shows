@extends('layouts.main')

@section('meta_title', 'Seriálovna.cz :: Login')
@section('meta_description', 'Seriálovna.cz :: Login')
@section('page_title', 'Login')


@section('content')
<div class="container login valign-wrapper">
    <div class="row">
        <div class="col l12 s12 m12">
            <form method="POST" action="{{ route('login') }}" class="valign-wrapper">
                {{ csrf_field() }}


                <div class="row">

                    <div class="input-field col s12 {{ $errors->has('email') ? ' invalid' : '' }}">
                       
                        <input id="email" placeholder="Email address" type="email" name="email" class="validate" value="{{ old('email') }}" required>
                        <label for="email">Email</label>
                    </div>


                    <div class="input-field col s12 {{ $errors->has('passeword') ? ' invalid' : '' }}">
                       
                        <input id="password" placeholder="Enter your password" type="password" name="password" class="validate" required>
                        <label for="password">Password</label>
                    </div>


                    <div class="col s12">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} />
                        <label for="remember">Remeber me</label>
                    </div>




                    <div class="col s12 center-align">
                        <button type="submit" class="btn login-submit blue darken-3">
                            Log In
                        </button>
                    </div>

                    <div class="col s12">
                        @if ($errors->has('email'))
                        <span>
                            <strong class="red">{{ $errors->first('email') }}</strong>
                        </span>
                        @endif

                        @if ($errors->has('password'))
                        <span >
                            <strong class="red">{{ $errors->first('password') }}</strong>
                        </span>
                        @endif
                    </div>


            </form>

            <div class="col s12 center-align">
                <a  href="{{ route('password.request') }}">
                    Forgot Your Password?
                </a>
            </div>
            
            <div class="col s12 center-align">
                <a  href="{{ route('register') }}">
                    Create an account.
                </a>
            </div>

        </div>
    </div>
</div>
</div>
@endsection
