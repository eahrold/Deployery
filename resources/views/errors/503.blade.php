@extends('layouts.app')

@section('content')
<div class="content">
    <div class="title">We don't offer this service.</div>
    <a class='btn btn-primary' href="{{ url()->previous() }}">Go back</a>

</div>
@endsection