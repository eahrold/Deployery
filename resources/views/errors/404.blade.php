@extends('layouts.app')

@section('content')
<div class="content">
    <div class="title">Hmm Not found?</div>
    <a class='btn btn-primary' href="{{ url()->previous() }}">Go back</a>
</div>
@endsection