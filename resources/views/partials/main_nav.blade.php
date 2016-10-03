 <div class="collapse navbar-collapse" id="app-navbar-collapse">
    <!-- Left Side Of Navbar -->

    <ul class="nav navbar-nav">
        @if (!Auth::guest())
        <li class="dropdown">
            <a href="#" class="dropdown-toggle"
                        data-toggle="dropdown"
                        role="button"
                        aria-expanded="false">
                Projects<span class="caret"></span>
            </a>
            <ul class="dropdown-menu" role="menu">
                @foreach(Project::findUserModels()->get() as $project)
                <li>
                    <a href="{{ route('projects.edit', $project->id) }}">
                        {{ $project->name }}
                    </a>
                </li>
                @endforeach
                <li>
                    <a href="{{ url('/projects/create') }}">
                        <b>Create New Project</b>
                    </a>
                </li>
            </ul>
        </li>
        <li>
            <a href="{{ url('/projects') }}">Dashbaord</a>
        </li>
        <li class="disabled"><a href="#">Account</a></li>
        @endif
    </ul>

    <!-- Right Side Of Navbar -->
    <ul class="nav navbar-nav navbar-right">
        <!-- Authentication Links -->
        @if (Auth::guest())
            <li><a href="{{ url('/login') }}">Login</a></li>
            <li><a href="{{ url('/register') }}">Register</a></li>
        @else
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                    {{ Auth::user()->username }} <span class="caret"></span>
                </a>

                <ul class="dropdown-menu" role="menu">
                    <li>
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
                    <li>
                        <a href="{{ route('users.edit',[ Auth::user()->id ]) }}">
                            <i class="fa fa-paw" aria-hidden="true"></i>My Account
                        </a>
                    </li>
                    @can('Manage', Auth::user())
                    <li>
                        <a href="{{ route('users.index') }}">
                            <i class="fa fa-btn fa-sign-out"></i>
                            Manage Users
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>
        @endif
    </ul>
</div>