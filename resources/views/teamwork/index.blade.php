@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12 my-4">
                <div class="card card-default">
                    <div class="card-header clearfix">
                        Teams
                        @can('manageTeams', Auth::user())
                        <a class="pull-right" href="{{route('teams.create')}}">
                            <i class="fa fa-plus-circle" aria-hidden="true"></i>
                        </a>
                        @endcan
                    </div>
                    <div class="card-body">
                        @if(!$teams->count())
                            <div>You don't have any teams, yet.</div>
                        @else
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Projects</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($teams as $team)
                                    <tr>
                                        <td>{{ $team->name }}</td>
                                        <td>{{ $team->projects->count() }}</td>
                                        <td>
                                            @if(auth()->user()->isOwnerOfTeam($team))
                                                <div class="label label-success w-100-px"> Owner </div>
                                            @else
                                                <div class="label label-primary w-100-px">Member</div>
                                            @endif
                                        </td>
                                        <td class='text-right'>
                                            @can('edit', $team)
                                                <a href="{{route('teams.edit', $team)}}" class="btn btn-sm btn-default">
                                                    <i class="fa fa-pencil"></i> Edit
                                                </a>
                                            @endif

                                            @can('switchToTeam', $team)
                                                <a href="{{ route('teams.switch', $team) }}" class="btn btn-sm btn-default w-100-px">
                                                    <i class="fa fa-sign-in"></i> Switch
                                                </a>
                                            @endcan

                                            @if(auth()->user()->current_team_id == $team->id)
                                                <div class="btn btn-sm btn-label w-100-px disable">Current team</div>
                                            @endif

                                            <a href="{{route('teams.members.show', $team)}}" class="btn btn-sm btn-default w-100-px">
                                                <i class="fa fa-users"></i> Members
                                            </a>

                                            @can('destroy', $team)
                                                <form style="display: inline-block;" action="{{route('teams.destroy', $team)}}" method="post">
                                                    {!! csrf_field() !!}
                                                    <input type="hidden" name="_method" value="DELETE" />
                                                    <button class="btn btn-danger btn-sm w-100-px"><i class="fa fa-trash-o"></i> Delete</button>
                                                </form>
                                            @elsecan('leave', $team)
                                                <form style="display: inline-block;"
                                                      action="{{ route('teams.members.leave', [$team, auth()->user()]) }}"
                                                      method="post">
                                                    {!! csrf_field() !!}
                                                    <input type="hidden" name="_method" value="DELETE" />
                                                    <button class="btn btn-warning btn-sm w-100-px"><i class="fa fa fa-sign-out fa-rotate-180"></i> Leave</button>
                                                </form>
                                            @elsecan('join', $team)
                                                <form style="display: inline-block;"
                                                      action="{{ route('teams.members.join', $team->id) }}"
                                                      method="post">
                                                    {!! csrf_field() !!}
                                                    <button class="btn btn-info btn-sm w-100-px"><i class="fa fa fa-sign-in"></i> Join</button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
