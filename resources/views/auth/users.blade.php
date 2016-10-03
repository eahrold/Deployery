@extends('layouts.app')

@section('content')

<div class="container container-lg">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Users
                    <span aria-hidden="true" class="pull-right">
                        <a href='{{ route("users.create") }}'>
                            <i class="fa fa-plus-circle" aria-hidden="true"></i>
                        </a>
                    </span>
                </div>

                <div class="panel-body">
                    <table class='table'>
                        <thead>
                            <th class='crunch-2x'>User Name</th>
                            <th class='crunch'>Email</th>
                            <th>Name</th>
                            <th class='crunch'>Admin</th>
                            <th class='crunch'></th>
                        </thead>
                        <tbody>
                        @foreach($models as $user)
                            <tr>
                                <td>
                                <a href='{{ route("users.edit", $user->id) }}' alt='edit'>
                                        {{ $user->username }}
                                    </a>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>{{ "{$user->first_name} {$user->last_name}" }}</td>
                                <td class='text-center'>
                                    @if($user->is_admin)
                                        <i class="fa fa-check" aria-hidden="true"></i>
                                    @endif
                                </td>
                                <td>
                                @if($user->id !== 1 && $user->id !== Auth::user()->id)
                                @include('includes.trash_button',
                                        ['route'=>route("users.destroy", $user->id)])
                                @else
                                    <i class="fa fa-user-secret" aria-hidden="true"></i>
                                @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
