
<li class="nav-item dropdown">
    <a href="#"
        class="nav-link dropdown-toggle"
        data-toggle="dropdown"
        role="button"
        aria-expanded="false">
        Projects
    </a>
    <ul class="dropdown-menu" role="menu">
        <?php $projects = Project::findUserModels()->get(); ?>
        <?php $vueRoute = request()->is('/') || request()->is('projects/*'); ?>

        @if($projects->count())
        @foreach( $projects as $project)
        <li class="dropdown-item">
            @if($vueRoute)
            <router-link :to="{ name: 'projects.info', params: { project_id: {{ $project->id }} }}">
                {{ $project->name }}
            </router-link>
            @else
            <a href="/projects/{{$project->id}}/info">{{ $project->name }}</a>
            @endif
        </li>
        @endforeach
        <li role="separator" class="dropdown-divider"></li>
        @endif
        <li class="dropdown-item">
            <router-link :to="{name: 'projects.create'}">
                Create New Project
            </router-link>
        </li>
    </ul>
</li>
