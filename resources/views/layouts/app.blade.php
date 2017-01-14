<!DOCTYPE html>
<html lang="en">
<head>

    <title>Project {{ $model->name or "Deployery" }}</title>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @if(Auth::user())
<!-- Form Data -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="pusherkey" content="{{ env('PUSHER_KEY') }}">
    <meta name="remember-token" content="{{ Auth::user()->remember_token }}">
    <meta name="api-token" content="{{ Auth::user()->api_token }}">
    @endif


    <!-- Fonts -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css"
          crossorigin="anonymous">

    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700">

    <!-- Styles -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.min.css"
          integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7"
          crossorigin="anonymous">

    <!-- Animate!! -->
    <link rel="stylesheet" type="text/css" href="/css/animate.css">

    <link href="{{ elixir('css/app.css') }}" rel="stylesheet">

    @yield('styles')

</head>
<body id="app-layout">
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button"
                        class="navbar-toggle collapsed"
                        data-toggle="collapse"
                        data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <a class="navbar-brand" href="{{ url('/') }}">
                    Deployery
                </a>
            </div>
            @include('partials.main_nav')
        </div>
    </nav>

    @include('includes.errors_flash')
    @yield('content')

    <footer class="footer">
      <div class="vertical-align">
        @yield('footer')
      </div>
    </footer>

    <script type="text/javascript" src="https://js.pusher.com/3.1/pusher.min.js"></script>
    <script src="{{ elixir('js/header-helper.js') }}"></script>

    @if(Auth::user())
    <script type="text/javascript">
        var globalHeaders = getGlobalApiHeaders();
    </script>
    @endif

    <script src="{{ elixir('js/app.js') }}"></script>
    <script src="{{ elixir('js/vue/vue-kit.js') }}"></script>
    <script src="{{ elixir('js/vendor.js') }}"></script>

    @yield('js')
    @yield('vue-js')
</body>
</html>
