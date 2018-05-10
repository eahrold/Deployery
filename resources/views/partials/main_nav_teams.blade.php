@if(Auth::user()->present()->shouldShowTeamMenu)
<li class="nav-item dropdown">
    <a href="#"
        class="nav-link dropdown-toggle"
        data-toggle="dropdown"
        role="button"
        aria-expanded="false">Teams
    </a>

    <ul class="dropdown-menu" role="menu">
        @if(Auth::user()->teams->count())
            @foreach(Auth::user()->teams as $team)
            <li class="dropdown-item">
                <a href="{{ route('teams.switch.alt', [$team, 'menu']) }}">
                    {!! $team->present()->menuName !!}
                </a>
            </li>
            @endforeach
        @else
            <li><a>Not currently a member of any teams</a></li>
        @endif

        @can('joinTeams', App\Models\Team::class)
        <li role="separator" class="divider"></li>
        <li class="dropdown-item">
            <a href="{{ url('/teams') }}">Show Teams</a>
        </li>
        @endcan
    </ul>
</li>
@endif