@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Register</div>
                <div class="panel-body">

                        {!! BootForm::open()->post()
                            ->action(url('/register'))
                            ->multipart()
                            ->role('form') !!}

                        {!! BootForm::text("Username", 'username') !!}
                        {!! BootForm::text("Email", 'email') !!}
                        {!! BootForm::text("First Name", 'first_name') !!}
                        {!! BootForm::text("Last Name", 'last_name') !!}

                        {!! BootForm::password("Password",'password') !!}
                        {!! BootForm::password("Confirm Password", 'password_confirmation') !!}

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-btn fa-user"></i> Register
                                </button>
                            </div>
                        </div>

                        {!! BootForm::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
