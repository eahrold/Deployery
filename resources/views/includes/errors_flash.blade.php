{{-- @if (count($errors) > 0)
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif --}}

@if (session('success'))
    <div class="flash-message">
        <div class="alert alert-success">
         {{ session('success') }}
        </div>
    </div>
@elseif(session('failure'))
    <div class="flash-message">
        <div class="alert alert-error">
             {{ session('success') }}
        </div>
    </div>
@endif