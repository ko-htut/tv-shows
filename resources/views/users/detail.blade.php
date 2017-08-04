@extends('layouts.main')

@section('meta_title', 'Seriálovna.cz :: User Profile')
@section('meta_description', 'Seriálovna.cz :: User Profile')
@section('page_title', 'User Profile')

@section('content')
<div class="container login valign-wrapper">
    <div class="row">
        <div class="col l12 s12 m12">
            <form method="POST" action="{{ route('users.create') }}" class="valign-wrapper">
                {{ csrf_field() }}

                <div class="row">

                    <div class="input-field col s12 {{ $errors->has('username') ? ' invalid' : '' }}">
                        <input id="username" placeholder="Your username" type="text" name="username" class="validate" value="{{ old('username') }}" required>
                        <label for="username">Username</label>
                    </div>

                    <div class="input-field col s12 {{ $errors->has('email') ? ' invalid' : '' }}">
                        <input id="email" placeholder="Your email address" type="email" name="email" class="validate" value="{{ old('email') }}" required>
                        <label for="email">Email</label>
                    </div>

                    
                    <div class="col s12 center-align">
                        <button type="submit" class="btn login-submit blue darken-3">
                            Edit
                        </button>
                    </div>

                    <div class="col s12">

                    </div>


            </form>


        </div>
    </div>
</div>
</div>
@endsection

