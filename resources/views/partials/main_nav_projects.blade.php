<li class="dropdown">
    <a href="#" class="dropdown-toggle"
                data-toggle="dropdown"
                role="button"
                aria-expanded="false">
        Projects<span class="caret"></span>
    </a>
    <ul class="dropdown-menu" role="menu">
        <?php $projects = Project::findUserModels()->get(); ?>
        @if($projects->count())
            @foreach( $projects as $project)
            <li>
                <a href="{{ route('projects.edit', $project->id) }}">
                    {{ $project->name }}
                </a>
            </li>
            @endforeach
            <li role="separator" class="divider"></li>
        @endif
        <li>
            <a href="{{ url('/projects/create') }}">
                Create New Project
            </a>
        </li>
    </ul>
</li>