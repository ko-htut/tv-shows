@extends('layouts.main')

@section('meta_title', 'Seriálovna.cz :: User Edit')
@section('meta_description', 'Seriálovna.cz :: User Edit')
@section('page_title', 'Editace uživatele')

@section('content')
<div class="container valign-wrapper">
    <div class="row">
        <h2>Editace uživatele</h2>
        <div class="col l12 s12 m12">
            <form method="POST" action="{{route('users.update', Auth::user()->id)}}" enctype="multipart/form-data">
                {{ csrf_field() }}
                {{ method_field('PUT') }}
                <input type="hidden" name="uId" value="{{ $user->id }}">
                <div class="row">

                    <div class="input-field col l6 m6 s12 {{ $errors->has('username') ? ' invalid' : '' }}">
                        <input id="username" placeholder="Tvoje uživatelské jméno" type="text" name="username" class="validate" value="{{ $user->username }}" required>
                        <label for="username">Uživatelské jméno</label>
                    </div>

                    <div class="input-field col l6 m6 s12 {{ $errors->has('email') ? ' invalid' : '' }}">
                        <input id="email" placeholder="Tvoje emailová adresa" type="email" name="email" class="validate" value="{{ $user->email }}" required>
                        <label for="email">Email</label>
                    </div>

                    <div class="input-field col l6 m6 s12 {{ $errors->has('first_name') ? ' invalid' : '' }}">
                        <input id="first_name" placeholder="Tvoje křestní jméno" type="text" name="first_name" class="validate" value="{{ $user->first_name }}">
                        <label for="first_name">Křestní jméno</label>
                    </div>


                    <div class="input-field col l6 m6 s12 {{ $errors->has('last_name') ? ' invalid' : '' }}">
                        <input id="last_name" placeholder="Tvoje příjmení" type="text" name="last_name" class="validate" value="{{ $user->last_name }}">
                        <label for="last_name">Příjmení</label>
                    </div>


                    <div class="input-field col l6 m6 s12">
                        <select class="icons" name="gender" id="gender">
                            <option value="" disabled selected>Vyberte pohlaví</option>
                            <option value="M" data-icon="/storage/app/public/img/placeholders/male.png" class="circle" @if($user->gender == "M") selected @endif>Muž</option>
                            <option value="F" data-icon="/storage/app/public/img/placeholders/female.png" class="circle" @if($user->gender == "F") selected @endif>Žena</option>
                        </select>
                        <label>Pohlaví</label>
                    </div>


                    <div class="input-field col l6 m6 s12 {{ $errors->has('birthday') ? ' invalid' : '' }}">
                        <input type="text" class="birthdaypicker" name="birthday" placeholder="Tvoje narozeniny" value="{{$user->birthday}}">
                        <label for="birthday">Narozeniny</label>
                    </div>


                    <div class="input-field col s12 {{ $errors->has('about') ? ' invalid' : '' }}">
                        <textarea id="about" name="about" placeholder="Něco o tobě" class="materialize-textarea" data-length="500">{{$user->about}}</textarea>
                        <label for="about">O mně</label>
                    </div>

                    <div class="col s12 center-align">
                        <img src="@if($user->avatar !== null){{$user->avatar->getSrc(100)}}?t={{strTotime($user->avatar->updated_at)}}@else{{$user->avatarPlaceholder()}}@endif" alt="" width="100"/>
                    </div>

                    <div class="file-field input-field">
                        <div class="btn">
                            <span>Avatar</span>
                            <input type="file" name="avatar" id="avatar" accept="image/jpg,image/jpeg,image/png,image/gif,image/svg">
                        </div>

                        <div class="file-path-wrapper">
                            <input class="file-path validate" type="text">
                        </div>

                    </div>



                    @foreach ($errors->all() as $msg)
                    <strong class="red">{{ $msg }}</strong><br/>
                    @endforeach

                    <div class="col s12 right-align">
                        <button type="submit" class="btn blue darken-3 omment-submit">
                            Edit
                        </button>
                    </div>
            </form>

        </div>
    </div>
</div>
</div>
@endsection

