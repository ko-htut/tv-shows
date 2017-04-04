 <!DOCTYPE html>
<html lang="{{ $lang }}">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no"/>
        <title>@yield('meta_title')</title>
        <meta name="description" content="@yield('meta_description')"> 
        <link rel="shortcut icon" href="/favicon.ico">
        <!-- CSS  -->
        <link href='http://fonts.googleapis.com/css?family=Pacifico' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.0/css/materialize.min.css">
        <link href="<?php echo url('/'); ?>/public/css/app.css" type="text/css" rel="stylesheet" media="screen,projection">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    </head>
    <body>
        <!-- JS -->
        <script src="https://code.jquery.com/jquery-2.2.4.min.js"   integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="   crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.0/js/materialize.min.js"></script>
        <script src="<?php echo url('/'); ?>/public/js/app.js"></script>
        @include('layouts.header')
        <div class="col s12 center">
            <h1></h1>
        </div>
        <div class="container1024">
           @yield('content')
        </div>
        @include('layouts.footer')
        <div id="hide">
    </body>
</html>

