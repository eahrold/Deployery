@extends('layouts.app')

@section('content')

@if(!$model->id)
{!! BootForm::open()->post()
                    ->action(route("servers.store", $project))
                    ->multipart()
                    ->role('form') !!}
@else

{!! BootForm::open()->put()
                    ->action(route("servers.update", [$model->project, $model]))
                    ->multipart()
                    ->role('form') !!}
{!! BootForm::bind($model) !!}
@endif

<div class="container container-lg">

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

                    @php $webhook = $model->webhook @endphp
                    @php $branch = $model->branch ?: $project->branch @endphp

                    <input type="hidden" name="webhook" value="{{ $webhook }}">
                    <h3>Host Settings</h3>
                    {!! BootForm::text('Name', 'name') !!}
                    {!! BootForm::text('Hostname', 'hostname') !!}
                    {!! BootForm::text("Port",'port')->value($model->port) !!}

                    <hr/>
                    <h3>Credentials</h3>
                    {!! BootForm::text("Username",'username') !!}
                    {!! BootForm::password("Password",'password') !!}

                    <div class='form-group'>
                        {!! BootForm::checkbox("Use SSH Key",'use_ssh_key') !!}
                        <a href="#sshkey" data-toggle="collapse">Show Public Key</a>
                        <div id="sshkey" class="collapse">
                            <textarea  class="form-control" rows="10" readonly>{{ $project->pubkey }}</textarea>
                        </div>
                    </div>

                    <hr/>
                    <h3>Deployment Info</h3>
                    {!! BootForm::text("Deployment Path",'deployment_path') !!}
                    {!! BootForm::text("Branch",'branch')->value($branch) !!}
                    {!! BootForm::text("Environment",'environment') !!}
                    {!! BootForm::text("Sub Directory",'sub_directory') !!}

                    <hr/>
                    <h3>Web Hooks</h3>
                    <div class='form-group'>
                        {!! BootForm::checkbox("AutoDeploy?",'autodeploy') !!}
                        <div><b>Webhook URL:</b> {{ $webhook }}</div>
                    </div>

                    <hr/>
                    <h3>Notifications</h3>
                    {!! BootForm::checkbox('Send Slack Notification', 'send_slack_messages') !!}
                    {!! BootForm::text('Slack Webhook URL', 'slack_webhook_url') !!}

                    @if($project->scripts->count())

                        @php $script_ids = isset($model->id) ? $model->scripts->pluck('id')->toArray() : []; @endphp
                        <hr/>
                        <h3>Install Scripts</h3>
                        @if($project->preinstall_scripts->count())
                        <label>Run these scripts before deployment</label>


                        @foreach($project->preinstall_scripts as $script)
                            @if(in_array($script->id, $script_ids))
                                {!! BootForm::checkbox("{$script->description}", 'script_ids[]')->value($script->id)->checked() !!}
                            @else
                                {!! BootForm::checkbox("{$script->description}", 'script_ids[]')->value($script->id) !!}
                            @endif
                        @endforeach
                        @endif

                        @if($project->postinstall_scripts->count())
                        <label>Run these scripts after deployments</label>
                        @foreach($project->postinstall_scripts as $script)
                            @if(in_array($script->id, $script_ids))
                                {!! BootForm::checkbox("{$script->description}", 'script_ids[]')->value($script->id)->checked() !!}
                            @else
                                {!! BootForm::checkbox("{$script->description}", 'script_ids[]')->value($script->id) !!}
                            @endif
                        @endforeach
                        @endif
                    @endif
                </div>
            </div>
            @include('includes.save_buttons')
        </div>
    </div>
</div>
{!! BootForm::close() !!}


@endsection
