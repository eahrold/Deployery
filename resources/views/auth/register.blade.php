@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Register') }}</div>

                <div class="card-body">

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
                            <button type="submit" class="btn btn-primary pull-right">
                                <i class="fa fa-btn fa-user"></i> Register
                            </button>
                        </div>

                        {!! BootForm::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
