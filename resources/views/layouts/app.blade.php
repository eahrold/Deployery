<!DOCTYPE html>
<html lang="en">
<head>

    <title>Project {{ $model->name or "Deployery" }}</title>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if(Auth::user())
<!-- Form Data -->
    <meta name="remember-token" content="{{ Auth::user()->remember_token }}">
    @endif

    <!-- Scripts -->
    <script>
    window.Deployery = {!!
        json_encode(['csrfToken' => csrf_token()])
    !!}

    @if(Auth::user() && $apiToken = Auth::user()->api_token)
        window.Deployery.apiToken = "{{ $apiToken }}";
        window.Deployery.userPubKey = "{!! Auth::user()->pubkey !!}";
        window.Deployery.pusherKey = "{{ env('PUSHER_KEY') }}"
        window.Deployery.user = {!! Auth::user()->toJson() !!}
    @endif

    </script>


    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700">


    <!-- Animate!! -->
    <link rel="stylesheet" type="text/css" href="/css/animate.css">

    <link href="{{ mix('/css/app.css') }}" rel="stylesheet">

    @yield('styles')

</head>
<body id="app-layout">
    <div id='app'>
        <nav class="navbar navbar-expand-md navbar-dark bg-dark">
            <div class="navbar-brand">
                Deployery
            </div>
            <button class="navbar-toggler"
                    type="button"
                    data-toggle="collapse"
                    data-target="#app-navbar-collapse"
                    aria-controls="pp-navbar-collapse"
                    aria-expanded="false"
                    aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            @include('partials.main_nav')
        </nav>

        @include('includes.errors_flash')
        @yield('content')

        <!-- Project Form -->
        <form-alert></form-alert>

        <footer class="footer">
          <div class="vertical-align">
            @yield('footer')
          </div>
        </footer>
    </div>

    <script type="text/javascript" src="https://js.pusher.com/3.1/pusher.min.js"></script>
    <script src="{{ mix('/js/app.js') }}"></script>
    @yield('js')
    @yield('vue-js')
</body>
</html>
