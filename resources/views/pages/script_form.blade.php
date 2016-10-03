@extends('layouts.app')

@section('content')

@if(!$model->id)
{!! BootForm::open()->post()
                    ->action(route("scripts.store", $project))
                    ->multipart()
                    ->role('form') !!}
@else

{!! BootForm::open()->put()
                    ->action(route("scripts.update", [$model->project, $model]))
                    ->multipart()
                    ->role('form') !!}
{!! BootForm::bind($model) !!}
@endif

<div class="container container-lg">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Install Scripts
                    <span class='pin-right'>
                    @if($model->id)
                        <a href='{{ route("projects.edit", $model->project) }}' alt='edit'>
                            <i class="fa fa-arrow-left" aria-hidden="true"></i>
                        </a>
                    </span>
                    @endif
                </div>

                <div id='main' class="panel-body tab-pane">
                    <div class="col-md-9">
                        {!! BootForm::hidden('id') !!}
                        {!! BootForm::text('Description', 'description') !!}
                        {!! BootForm::textarea('Script', 'script') !!}

                        {!! BootForm::checkbox('Run before deployment?', 'run_pre_deploy') !!}
                        {!! BootForm::checkbox('Stop on failure?', 'stop_on_failure') !!}

                        {!! BootForm::select('Which Deployment?', 'on_deployment')
                                    ->options($model->deployment_opts)
                                    ->select($model->on_deployment ?: $model::RUN_ON_ALL_DEPLOYMENTS); !!}

                        <label>Execute on these servers</label>
                        @php $server_ids = $model->servers->pluck('id')->toArray() @endphp
                        @foreach($project->servers as $server)
                            @if(in_array($server->id, $server_ids))
                                {!! BootForm::checkbox("{$server->name}", 'server_ids[]')->value($server->id)->checked() !!}
                            @else
                                {!! BootForm::checkbox("{$server->name}", 'server_ids[]')->value($server->id) !!}
                            @endif
                        @endforeach

                        {!! BootForm::checkbox('Make available to all servers?', 'available_to_all_servers') !!}
                    </div>
                    <div class="col-md-3 script-variables">
                        <h4>Script Variables</h4>
                        <div>You can substitute the following variables in your script</div>
                        <br/>
                        @foreach($model->parsable as $key => $description)
                        <div class="form-group">
                            <div class='variable'>{{ $key }}</div>
                            <div class='description'>{{ $description }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @include('includes.save_buttons')
        </div>
    </div>
</div>

{!! BootForm::close() !!}


@endsection
