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
    <meta name="remember-token" content="{{ Auth::user()->remember_token }}">
    @endif

    <!-- Scripts -->
    <script>
    window.Laravel = {!!
        json_encode(['csrfToken' => csrf_token()])
    !!}
    @if(Auth::user() && $apiToken = Auth::user()->api_token)
        window.Laravel.apiToken = "{{ $apiToken }}";
        window.Laravel.userPubKey = "{!! Auth::user()->pubkey !!}";
        window.Laravel.pusherKey = "{{ env('PUSHER_KEY') }}"
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
                    <div class="navbar-brand">
                        Deployery
                    </div>
                </div>
                @include('partials.main_nav')
            </div>
        </nav>

        @include('includes.errors_flash')
        @yield('content')

        <!-- Project Form -->
        <project-form endpoint='/api/projects'></project-form>

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
