@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card card-default my-4">
                    <div class="card-header clearfix">
                        Members of team "{{$team->name}}"
                        <a href="{{route('teams.index')}}" class="pull-right">
                            <i class="fa fa-arrow-left"></i>
                        </a>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th class="text-right">Action</th>
                            </tr>
                            </thead>
                            @foreach($team->users as $user)
                                <tr>
                                    <td>{{ $user->username }}</td>
                                    <td class="text-right">
                                        @if(auth()->user()->isOwnerOfTeam($team))
                                            @if(auth()->user()->getKey() !== $user->getKey())
                                               @include('teamwork.partials.delete_button')
                                            @else
                                                <b>Owner</b>
                                            @endif
                                        @elseif(auth()->user()->getKey() === $user->getKey())
                                            @include('teamwork.partials.leave_button')
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>

                @if($team->invites->count())
                <div class="card card-default mb-4">
                    <div class="card-header clearfix">Pending invitations</div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>E-Mail</th>
                                <th>Invited on</th>
                                <th class="text-right">Action</th>
                            </tr>
                            </thead>
                            @foreach($team->invites as $invite)
                                <tr>
                                    <td>{{ $invite->email }}</td>
                                    <td>{{ $invite->updated_at }}</td>
                                    <td class="text-right">
                                        {{-- @can('invite', $team) --}}
                                        <a href="{{route('teams.members.resend_invite', $invite)}}" class="btn btn-sm btn-default">
                                            <i class="fa fa-envelope-o"></i> Resend invite
                                        </a>
                                        {{-- @endcan --}}
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
                @endif

                @can('invite', $team)
                <div class="card card-default mb-4">
                    <div class="card-header clearfix">Invite to team "{{$team->name}}"</div>
                    <div class="card-body">
                        <form class="form-inline" method="post" action="{{route('teams.members.invite', $team)}}">
                            {!! csrf_field() !!}
                            <div class="w-100 form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label class="col-2 control-label">E-Mail Address</label>

                                <div class="col-8">
                                    <input type="email" class="form-control w-100" name="email" value="{{ old('email') }}">

                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                                <strong>{{ $errors->first('email') }}</strong>
                                            </span>
                                    @endif
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-btn fa-envelope-o"></i>Invite to Team
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @endcan

            </div>
        </div>
    </div>
@endsection
