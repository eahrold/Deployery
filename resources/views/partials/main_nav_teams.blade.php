<li class="dropdown">
    <a href="#" class="dropdown-toggle"
                data-toggle="dropdown"
                role="button"
                aria-expanded="false">
        Teams<span class="caret"></span>
    </a>
    <ul class="dropdown-menu" role="menu">
        @foreach(Auth::user()->teams as $team)
        <li>
            <a href="{{ route('teams.switch.alt', [$team, 'menu']) }}">
                {{ $team->name }}
            </a>
        </li>
        @endforeach
        <li role="separator" class="divider"></li>
        <li>
            <a href="{{ url('/teams') }}">Manage Teams</a>
        </li>
    </ul>
</li>