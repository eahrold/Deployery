@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card card-default my-4">
                    <div class="card-header">Edit team {{$team->name}}</div>
                    <div class="card-body">
                        <form class="form-horizontal" method="post" action="{{route('teams.update', $team)}}">
                            <input type="hidden" name="_method" value="PUT" />
                            {!! csrf_field() !!}

                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label class="col-12 control-label">Name</label>

                                <div class="col-12">
                                    <input type="text" class="form-control" name="name" value="{{ old('name', $team->name) }}">

                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>


                            <div class="form-group">
                                <div class="col">
                                    <button type="submit" class="btn btn-primary pull-right">
                                        <i class="fa fa-btn fa-save"></i>Save
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
