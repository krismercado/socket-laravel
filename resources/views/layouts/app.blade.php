<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'JackHammer') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
      .round-button {
          display:block;
          width:20px;
          height:20px;
          line-height:20px;
          border: 2px solid #f5f5f5;
          border-radius: 50%;
          color:#f5f5f5;
          text-align:center;
          text-decoration:none;
          background: #555777;
          box-shadow: 0 0 3px gray;
          font-size:15px;
          font-weight:bold;
      }
      .round-button:hover {
          background: #777555;
      }

      body {
      }

      .board {
        transform: rotate(-45deg);
      }

      .board2 > div {
        #border: 1px solid black;
        width: 60px;
        height: 60px;
        background: #fff;
        margin-bottom: -10px;
        padding: 10px;
        display: inline-block;
      }

      .closes::before {
        content: 'X';
        color: black;
        font-family: 'Helvetica', 'Arial', sans-serif;
        font-size: 3.5em;
        font-weight: bold;
      }

      td {
        border: 1px solid black;
        height: 50px;
        width: 50px;
      }

      .dotBorder {
        width: 90%;
        height: 90%;
        padding: 5%;
        background: #000 !important;
        border-radius: 100%;
      }

      .redDot {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: #ad0101 !important;
      }

      .greenDot {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: #008000 !important;
      }

      .blueDot {
        width: 100%;
        height: 100%;
        box-sizing: border-box;
        border-radius: 50%;
        background: #3F51B5 !important;
      }

      .whiteDot {
        width: 100%;
        height: 100%;
        box-sizing: border-box;
        border-radius: 50%;
        background: #fff !important;
      }

      .selected {
        background: yellow !important;
      }

      .occupied {
        background: orange !important;
      }

      #instructions {
        background: #fff;
        padding: 5px;
        color: #ad0101;
        margin: 15vmin auto 0 auto;
        width: 60vmin;
        text-align: center;
        font-size: 18px;
        border: 2px solid black;
      }

      .active{
        background-color:red;
      }

    </style>

    <style>
    .margin-bottom {
    margin-bottom: 15px;
    }

    .fade-enter, .fade-leave-active {
    opacity: 0;
    }

    .fade-enter-active, .fade-leave-active {
    transition: opacity .5s;
    }
    </style>

  </head>
  <body>
    <div id="app">
      @yield('content')
    </div>
  </body>
</html>
