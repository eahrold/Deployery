@extends('layouts.app')

@section('content')

<div class="container container-lg">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
            @if(!$projects->count())

                <div class="panel-body">
                    <div class='row text-center'>
                        <a href='{{ route("projects.create") }}'>
                            <h3>Add your first project</h3>
                        </a>
                    </div>
                </div>
            @else

                <div class="panel-heading">
                    Projects
                    <span aria-hidden="true" class="pull-right">
                        <a href='{{ route("projects.create") }}'>
                            <i class="fa fa-plus-circle" aria-hidden="true"></i>
                        </a>
                    </span>
                </div>

                <div class="panel-body">
                    <table class='table'>
                        <thead>
                            <th>Name</th>
                            <th>Last Deployed</th>
                            <th class='crunch'>Servers</th>
                        </thead>
                        <tbody>
                        @foreach($projects as $project)
                            <tr>
                                <td>
                                    <a href='{{ route("projects.edit", $project->id) }}' alt='edit'>
                                        {{ $project->name }}
                                    </a>
                                </td>
                                <td>
                                    @if($first = $project->history->first())
                                        <strong>{{ $first->server->name }}:</strong>
                                        {{ $first->present()->created_at }}
                                    @else
                                        Never deployed
                                    @endif
                                </td>
                                <td>{{ $project->servers->count() }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            </div>
        </div>
    </div>
</div>

@endsection
