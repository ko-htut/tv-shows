<!DOCTYPE html>
<html lang="{{ $layout['lang'] }}">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <title>@yield('meta_title', 'Televizní seriály - Seriálovna')</title>
        <meta name="description" content="@yield('meta_description')"> 
        <meta name="robots" content="index, follow" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no"/>
        <meta name="author" content="Jan Neterda" />
        <meta name="keywords" content="Televizní seriály, Televizní epizody, TV seriály" />
        <link rel="shortcut icon" href="/favicon.ico">
        <!-- CSS  -->
        <link href='http://fonts.googleapis.com/css?family=Pacifico' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.0/css/materialize.min.css">
        
        
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.4.2/css/swiper.min.css" />
        
        
        <link href="<?php echo url('/'); ?>/public/css/app.css" type="text/css" rel="stylesheet" media="screen,projection">
       
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
     
    </head>
    <body>
        <!-- JS -->
        <script src="https://code.jquery.com/jquery-2.2.4.min.js"   integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="   crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.0/js/materialize.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.4.2/js/swiper.min.js"></script>
        
        <script src="<?php echo url('/'); ?>/public/js/app.js"></script>
        <script src="<?php echo url('/'); ?>/public/js/init.js"></script>
        @include('layouts.header')
        <div class="col s12 center">
         
        </div>
        
        <div class="container">
            @yield('content')
        </div>
        @include('layouts.footer')
    </body>
</html>

