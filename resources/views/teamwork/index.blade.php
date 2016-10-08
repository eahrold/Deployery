@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading clearfix">
                        Teams
                        @can('manageTeams', Auth::user())
                        <a class="pull-right btn btn-default btn-sm" href="{{route('teams.create')}}">
                            <i class="fa fa-plus"></i> Create team
                        </a>
                        @endcan
                    </div>
                    <div class="panel-body">
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
                                                <span class="label label-success">Owner</span>
                                            @else
                                                <span class="label label-primary">Member</span>
                                            @endif
                                        </td>
                                        <td class='text-right'>
                                            @if(auth()->user()->isOwnerOfTeam($team))
                                                <a href="{{route('teams.edit', $team)}}" class="btn btn-sm btn-default">
                                                    <i class="fa fa-pencil"></i> Edit
                                                </a>
                                            @endif

                                            @if(is_null(auth()->user()->currentTeam) || auth()->user()->currentTeam->getKey() !== $team->getKey())
                                                <a href="{{route('teams.switch', $team)}}" class="btn btn-sm btn-default">
                                                    <i class="fa fa-sign-in"></i> Switch
                                                </a>
                                            @else
                                                <span class="label label-default">Current team</span>
                                            @endif

                                            <a href="{{route('teams.members.show', $team)}}" class="btn btn-sm btn-default">
                                                <i class="fa fa-users"></i> Members
                                            </a>

                                            @if(auth()->user()->isOwnerOfTeam($team))

                                                <form style="display: inline-block;" action="{{route('teams.destroy', $team)}}" method="post">
                                                    {!! csrf_field() !!}
                                                    <input type="hidden" name="_method" value="DELETE" />
                                                    <button class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i> Delete</button>
                                                </form>
                                            @elseif(auth()->user()->isTeamMember($team))
                                                <form style="display: inline-block;"
                                                      action="{{ route('teams.members.leave', [$team, auth()->user()]) }}"
                                                      method="post">
                                                    {!! csrf_field() !!}
                                                    <input type="hidden" name="_method" value="DELETE" />
                                                    <button class="btn btn-warning btn-sm"><i class="fa fa fa-sign-out"></i>{{ " Leave" }}</button>
                                                </form>
                                            @else
                                                <form style="display: inline-block;"
                                                      action="{{ route('teams.members.join', $team->id) }}"
                                                      method="post">
                                                    {!! csrf_field() !!}
                                                    <button class="btn btn-info btn-sm"><i class="fa fa fa-sign-out"></i>Join Now</button>
                                                </form>
                                            @endif
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
