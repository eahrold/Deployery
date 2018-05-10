 <div class="collapse navbar-collapse" id="app-navbar-collapse">
    <!-- Left Side Of Navbar -->

    <ul class="navbar-nav">
        @if (!Auth::guest())
            @include('partials.main_nav_projects')
            @include('partials.main_nav_teams')
            <li class="nav-item">
                @if(request()->is('/') || request()->is('projects/*'))
                <router-link class='nav-link' :to="{ name: 'projects.list'}">Dashbaord</router-link>
                @else
                <a class='nav-link' href="/">Dashboard</a>
                @endif
            </li>
        @endif

        @if (Auth::guest())
            <li class="nav-item">
                <a class='nav-link' href="{{ url('/login') }}">Login</a>
            </li>
            <li class="nav-item">
                <a class='nav-link' href="{{ url('/register') }}">Register</a>
            </li>
        @else
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ Auth::user()->email }}
                </a>

                <ul class="dropdown-menu" role="menu">
                    <li class="dropdown-item nav-item">
                        <a href="{{ url('/logout') }}"
                            onclick="event.preventDefault();
                                     document.getElementById('logout-form').submit();">
                            <i class="fa fa-btn fa-sign-out"></i>Logout
                        </a>
                        <form id="logout-form" action="{{ url('/logout') }}"
                             method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </li>
                    <li class="dropdown-item nav-item">
                        <router-link :to="{ name: 'my.account'}">My Account</router-link>
                    </li>
                    @can('Manage', Auth::user())
                    <li class="dropdown-item nav-item">
                        <a href="{{ route('users.index') }}">
                            <i class="fa fa-btn fa-users"></i>
                            Manage Users
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>
        @endif
    </ul>
</div>