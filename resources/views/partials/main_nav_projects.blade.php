<li class="dropdown">
    <a href="#" class="dropdown-toggle"
                data-toggle="dropdown"
                role="button"
                aria-expanded="false">
        Projects<span class="caret"></span>
    </a>
    <ul class="dropdown-menu" role="menu">
        <?php $projects = Project::findUserModels()->get(); ?>
        <?php $vueRoute = request()->is('/') || request()->is('projects/*'); ?>

        @if($projects->count())
            @foreach( $projects as $project)
            <li>
                @if($vueRoute)
                <router-link :to="{ name: 'projects.info', params: { project_id: {{ $project->id }} }}">
                    {{ $project->name }}
                </router-link>
                @else
                <a href="/projects/{{$project->id}}/info">{{ $project->name }}</a>
                @endif
            </li>
            @endforeach
            <li role="separator" class="divider"></li>
        @endif
        <li>
            <a data-toggle="modal"
               data-target="#projectForm">
                Create New Project
            </a>
        </li>
    </ul>
</li>
