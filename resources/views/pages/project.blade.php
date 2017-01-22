@extends('layouts.app')

@section('content')

@if($model->id)

<project></project>

@else
    <div class="container container-lg">
        <h3>Create a new project</h3>
        @include('partials.project_form')
    </div>
@endif

@endsection

