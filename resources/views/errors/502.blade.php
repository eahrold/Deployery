@extends('layouts.app')

@section('content')
<div class="content">
    <div class="title">You've found a bad gateway... Could be the Demogorgon</div>
    <a class='btn btn-primary' href="{{ url()->previous() }}">Go back</a>
</div>
@endsection