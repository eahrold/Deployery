<div style="width: 100%; height: 100%; text-align: center; position: relative; font-family: Arial, sans-serif;">
    <div style="margin: 0 auto; width: 90%; height: 400px; border: 1px solid #efefef; border-color: gray; border-radius: 4px">

        <div style='height: 50px; background: #efefef;padding-top: 10px; font-weight: 400; font-size: 24'>
            <span style="padding: 0 10">
                <img src="<?php echo $message->embed(public_path('assets/icon-deployery-xs.png')); ?>">
            </span>
                You have been invited to join a Deployery team
            <span style="padding: 0 10">
                <img src="<?php echo $message->embed(public_path('assets/icon-deployery-xs.png')); ?>">
            </span>
        </div>

        <h3>
        {{ $team->owner->full_name }} wants you to be part of the {{$team->name}} team.
        </h3>

        <div style="padding: 10px; font-weight: 400">
            Click here to join<br/>
            <a style="color:#337ab7"
               href="{{ route('teams.accept_invite', $invite->accept_token) }}">
                {{ route('teams.accept_invite', $invite->accept_token) }}
            </a>
        </div>

        <div style="padding: 200px; font-weight: 400"></div>
        <img src="<?php echo $message->embed(public_path('assets/icon-deployery-sm-bk.png')); ?>">
    </div>
</div>

