<form style="display: inline-block;" action="{{ route('teams.members.leave', [$team, $user]) }}" method="post">
    {!! csrf_field() !!}
    <input type="hidden" name="_method" value="DELETE" />
    <button class="btn btn-warning btn-xs w-100-px"><i class="fa fa-trash-o"></i> {{ "Leave" }}</button>
</form>