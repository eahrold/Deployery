@extends('layouts.app')

@section('content')

<div class="container content">

@if(!$model->id)
{!! BootForm::open()->post()
                    ->action(route("configs.store", $project))
                    ->multipart()
                    ->role('form') !!}
@else
{!! BootForm::open()->put()
                    ->action(route("configs.update", [$model->project, $model]))
                    ->multipart()
                    ->role('form') !!}
{!! BootForm::bind($model) !!}
@endif

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Configuration File
                    @if($model->id)
                    <span class='pin-right'>
                        <a href='{{ route("projects.edit", $model->project) }}' alt='edit'>
                            </i>Back to Project
                        </a>
                    </span>
                    @endif
                </div>

                <div id='main' class="panel-body tab-pane">
                        {!! BootForm::hidden('id') !!}
                        {!! BootForm::text('Path', 'path') !!}
                        {!! BootForm::textarea('File Contents', 'contents') !!}

                        <label>Upload To These Servers</label>
                        @php $server_ids = $model->servers->pluck('id')->toArray() @endphp
                        @foreach($project->servers as $server)
                            @if(in_array($server->id, $server_ids))
                                {!! BootForm::checkbox("{$server->name}", 'server_ids[]')->value($server->id)->checked() !!}
                            @else
                                {!! BootForm::checkbox("{$server->name}", 'server_ids[]')->value($server->id) !!}
                            @endif
                        @endforeach
                </div>

            </div>
            @include('includes.save_buttons')
        </div>
    </div>
{!! BootForm::close() !!}
</div>

@endsection
