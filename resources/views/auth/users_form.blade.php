@extends('layouts.app')

@section('content')

@if(!$model->id)
{!! BootForm::open()->post()
                    ->action(route("users.store"))
                    ->multipart()
                    ->role('form') !!}
@else

{!! BootForm::open()->put()
                    ->action(route("users.update", [$model]))
                    ->multipart()
                    ->role('form') !!}
{!! BootForm::bind($model) !!}
@endif

<div class="container container-lg">
    <div class="row">
        <div class="col-md-12 my-4">
            <div class="card card-default">
                <div class="card-header">
                    <span>User</span>
                    <span class='pull-right'>
                    @if($model->id)
                        <a href='{{ route("users.index") }}' alt='edit'>
                            <i class="fa fa-arrow-left" aria-hidden="true"></i>
                        </a>
                    </span>
                    @endif
                </div>

                <div id='main' class="card-body tab-pane">

                    {!! BootForm::hidden('id') !!}

                    {!! BootForm::text("Username", 'username') !!}
                    {!! BootForm::text("Email", 'email') !!}
                    {!! BootForm::text("First Name", 'first_name') !!}
                    {!! BootForm::text("Last Name", 'last_name') !!}

                    {!! BootForm::password("Password",'password') !!}
                    {!! BootForm::password("Confirm Password", 'password_confirmation') !!}

                    @if($model->id)
                    <div>
                        <a href="#sshkey" data-toggle="collapse">
                            SSH Key Used to authenticate to the GitHub/Bitbucket/etc
                        </a>
                        <div id="sshkey" class="collapse">
                            <textarea  class="form-control" rows="8">{{ $model->pubkey }}</textarea>
                        </div>
                    </div>
                    @endif

                     @can("manage", Auth::user())
                        {!! BootForm::checkbox("Is Admin", 'is_admin') !!}
                        {!! BootForm::checkbox("Can Manage Teams", 'can_manage_teams') !!}
                        {!! BootForm::checkbox("Can Join Any Team", 'can_join_teams') !!}
                    @endcan

                </div>
            </div>
            @include('includes.save_buttons')
        </div>
    </div>
</div>
{!! BootForm::close() !!}


@endsection
