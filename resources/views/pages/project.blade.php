@extends('layouts.app')

@section('content')

@if($model->id)
<div id='project-vm'>
    <nav class="navbar navbar-default navbar-nocollapse" >
        <div class='navbar-center'>
            <ul class="nav navbar-nav">
                <li class="active">
                    <a data-toggle="tab" href="#overview">
                        <i class="fa fa-dashboard" aria-hidden="true"></i>
                        <span class='hidden-sm hidden-xs'>Overview</span>
                    </a>
                </li>

                <li>
                    <a data-toggle="tab" href="#servers" :class="cloning ? disabled:''">
                        <i class="fa fa-server" aria-hidden="true"></i>
                        <span class='hidden-sm hidden-xs'>Servers</span>
                    </a>
                </li>
                <li>
                    <a data-toggle="tab" href="#history" :class="cloning ? disabled:''">
                        <i class="fa fa-history" aria-hidden="true"></i>
                        <span class='hidden-sm hidden-xs'>History</span>
                    </a>
                </li>
                @if($model->servers->count())
                <li>
                    <a data-toggle="tab" href="#config">
                        <i class="fa fa-cog" aria-hidden="true"></i>
                        <span class='hidden-sm hidden-xs'>Config</span>
                    </a>
                </li>
                <li>
                    <a data-toggle="tab" href="#scripts">
                        <i class="fa fa-file-code-o" aria-hidden="true"></i>
                        <span class='hidden-sm hidden-xs'>Scripts</span>
                    </a>
                </li>
                @endif
                <li>
                    <a data-toggle="tab" href="#project">
                        <i class="fa fa-rocket" aria-hidden="true"></i>
                        <span class='hidden-sm hidden-xs'>Project Info</span>
                    </a>
                </li>
                <li>
                    <a data-toggle="tab" href="#delete">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                        <span class='hidden-sm hidden-xs'>Danger Zone</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Cloning Status message -->
    <div class="container container-lg">
        @include('partials.project_cloning', ['project'=>$model])
    </div>

    <div class="container container-lg">
        <!-- tabs content -->
        <div class="tab-content col-md-12">

            <!-- Overview -->
            <div class='tab-pane active' id='overview'>
                <div>
                    @include('partials.project_info_project', ['project'=>$model])
                    @include('partials.project_info_deployment', ['project'=>$model])
                </div>
            </div>

            <!-- Servers -->
            <div class="tab-pane" id="servers">
                <servers :servers='project.servers'
                         :project-id='project.id'>
                </servers>
            </div>

            <!-- History -->
            <div id='history' class="tab-pane fade">
                <history :history='project.history'></history>
            </div>

            <!-- Configuration Files -->
            <div id='config' class="tab-pane fade">
                <configs :configs='project.configs'
                         :project-id='project.id'>
                </configs>
            </div>

            <!-- Install Scripts -->
            <div id='scripts' class="tab-pane fade">
                <scripts :scripts='project.scripts'
                         :project-id='project.id'>
                </scripts>
            </div>

            <!-- Project Details -->
            <div id='project' class="tab-pane {{$model->id ? 'fade': 'active' }}">
                @include('partials.project_form')
            </div>

            <!-- Delete Area -->
            <div id="delete" class="tab-pane">
                <div class="panel panel-default">
                    <div class='panel-body'>
                        <div class='row row-centered'>
                            <div class='col-md-4 col-centered'>
                                 {!! BootForm::open()
                                             ->id('project-submit')
                                             ->delete()
                                             ->action(route($model->getTable().".destroy", $model->id))
                                             ->role('form') !!}

                                {!! BootForm::text('Type the name of your project to delete', 'name')
                                            ->id('confirm-project-name') !!}
                                {!! BootForm::submit("Delete")
                                            ->id("delete-button")
                                            ->addClass('btn-danger btn delete')
                                            ->attribute('disabled','disabled') !!}
                                {!! BootForm::close() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end tabs content -->
      </div>

        <!-- Cloning Status message -->
        <div class="container container-lg">
            @include('partials.project_deploying', ['project'=>$model])
        </div>
    </div>
</div>

@else
    <div class="container container-lg">
        <h3>Create a new project</h3>
        @include('partials.project_form')
    </div>
@endif

@endsection

@section('js')

<script type="text/javascript">
// Vue Model
var isDeploying = '{!! $model->is_deploying !!}' ? true : false;
var isCloning = '{!! $model->is_cloning !!}' ? true : false;
var project = {!! $model->toJson() !!};
var projectVue = CreateProjectVue("#project-vm", project, isDeploying);

</script>

<script type="text/javascript">
/*
 * Simple confirmation step required to delete project.
 */
$("#confirm-project-name").on("change keyup paste", function(){
    $("#delete-button").prop('disabled', project.name.toLowerCase() != this.value.toLowerCase());
});
</script>

@append