@extends('layouts.app')

@section('content')



<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <ul class="nav nav-pills">
                        <li class="active"><a data-toggle="tab" href="#project">Project Details</a></li>
                        @if($model->id)
                        <li><a data-toggle="tab" href="#servers">Servers</a></li>
                        <li><a data-toggle="tab" href="#config">Config Files</a></li>
                        @endif
                    </ul>
                </div>
                <div  class="panel-body tab-content">
                    <div id='project' class="tab-pane active">
                        @include('partials.project_project_tab', ['servers'=>$model->servers])
                    </div>

                    <div id='servers' class="tab-pane fade">
                        @include('partials.project_server_tab', ['servers'=>$model->servers])
                    </div>

                    <div id='config' class="tab-pane fade">
                        @include('partials.project_config_tab', ['config'=>$model->config])
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
@yield('deploy-js')
@endsection