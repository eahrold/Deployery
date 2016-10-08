@if(Auth::user()->present()->shouldShowTeamMenu)
<li class="dropdown">
    <a href="#" class="dropdown-toggle"
                data-toggle="dropdown"
                role="button"
                aria-expanded="false">Teams
        <span class="caret"></span>
    </a>
    <ul class="dropdown-menu" role="menu">
        @if(Auth::user()->teams->count())
            @foreach(Auth::user()->teams as $team)
            <li>
                <a href="{{ route('teams.switch.alt', [$team, 'menu']) }}"'>
                    @if(Auth::user()->currentTeam->id == $team->id)
                    <b>{{ $team->name }}</b>
                    @else
                    {{ $team->name }}
                    @endif
                </a>
            </li>
            @endforeach
        @else
            <li><a>Not currently a member of any teams</a></li>
        @endif
        @can('manageTeams', Auth::user())
        <li role="separator" class="divider"></li>
        <li>
            <a href="{{ url('/teams') }}">Manage Teams</a>
        </li>
        @endcan
    </ul>
</li>
@endif