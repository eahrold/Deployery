@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Projects
                    <a href='{{ route("projects.create") }}'>
                        <i class="fa fa-plus-circle" aria-hidden="true"></i>
                    </a>
                </div>

                <div class="panel-body">
                    <table class='table'>
                        <thead>
                            <th>Name</th>
                            <th>Servers</th>
                            <th class='crunch'></th>
                            <th class='crunch'></th>
                        </thead>
                        <tbody>
                        @foreach($projects as $project)
                            <tr>
                                <td>
                                    <a href='{{ route("projects.edit", $project->id) }}' alt='edit'>
                                        {{ $project->name }}
                                    </a>
                                </td>
                                <td>{{ $project->servers->count() }}</td>
                                <td>
                                    <a href='{{ route("projects.edit", $project->id) }}' alt='edit'>
                                        <i class="fa fa-pencil-square-o" aria-hidden="true" alt='delete'></i>
                                    </a>
                                </td>
                                <td>

                                @include('includes.trash_button', ['route'=>route("projects.destroy", $project->id)])

                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
