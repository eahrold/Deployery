@extends('layouts.app')

@section('content')

@if(!$model->id)
{!! BootForm::open()->post()
                    ->action(route("projects.{projects}.servers.store", $project))
                    ->multipart()
                    ->role('form') !!}
@else

{!! BootForm::open()->put()
                    ->action(route("projects.{projects}.servers.update", [$model->project, $model]))
                    ->multipart()
                    ->role('form') !!}
{!! BootForm::bind($model) !!}
@endif

<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Server Config
                    <span class='pin-right'>
                    @if($model->id)
                        <a href='{{ route("projects.edit", $model->project) }}' alt='edit'>
                            <i class="fa fa-arrow-left" aria-hidden="true"></i>
                        </a>
                    </span>
                    @endif
                </div>

                <div id='main' class="panel-body tab-pane">

                        {!! BootForm::hidden('id') !!}

                        {{-- */$webhook = $model->webhook /* --}}
                        <input type="hidden" name="webhook" value="{{$webhook}}">
                        <h3>Host Settings</h3>
                        {!! BootForm::text('Name', 'name') !!}
                        {!! BootForm::text('Hostname', 'hostname') !!}
                        {!! BootForm::text("Port",'port') !!}

                        <h3>Credentials</h3>
                        {!! BootForm::text("Username",'username') !!}
                        {!! BootForm::password("Password",'password') !!}

                        <div class='form-group'>
                            {!! BootForm::checkbox("Use SSH Key",'use_ssk_key') !!}
                            <a href="#sshkey" data-toggle="collapse">Show Public Key</a>
                            <div id="sshkey" class="collapse">
                                <textarea  class="form-control" rows="8">{{ Project::find($project)->pubkey }}</textarea>
                            </div>

                        </div>

                        <h3>Deployment Info</h3>
                        {!! BootForm::text("Deployment Path",'deployment_path') !!}
                        {!! BootForm::text("Branch",'branch') !!}
                        {!! BootForm::text("Environment",'environment') !!}
                        {!! BootForm::text("Sub Directory",'sub_directory') !!}

                        <div class='form-group'>
                            {!! BootForm::checkbox("AutoDeploy?",'autodeploy') !!}
                            <div><b>Webhook URL:</b> {{ $webhook }}</div>
                        </div>
                </div>
            </div>
            @include('includes.save_buttons')
        </div>
    </div>
</div>

{!! BootForm::close() !!}

@endsection

@section('js')
<script type="text/javascript">
    $('textarea').autoResize();
</script>
@endsection