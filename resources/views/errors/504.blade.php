@extends('layouts.app')

@section('content')
<div class="content">
    <div class="title">The request took way too long. Try again in a little while</div>
    <a class='btn btn-primary' href="{{ url()->previous() }}">Go back</a>
</div>
@endsection