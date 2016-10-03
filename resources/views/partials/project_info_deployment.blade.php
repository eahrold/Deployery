 <div class="col-md-12">
    <div class="panel panel-default">
        <div class="pannel-nav navbar navbar-default navbar-static">
            <div class='nav navbar-nav navbar-left'>
                Deployments
            </div>
        </div>

        <div class='panel-body'>
            <div class='row'>
                <div class="col-md-12">
                    <div class='pull-left'>Today</div>
                    <div class='pull-right'>
                        <code>
                            {{ $project->history()
                                       ->where('created_at','>', Carbon::now()
                                       ->subDay())
                                       ->count()
                            }}
                        </code>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class="col-md-12">
                    <div class='pull-left'>All Time</div>
                    <div class='pull-right'><code>{{ $project->history->count() }}</code></div>
                </div>
            </div>

            @if($last =  $project->history->last())
            <div class='row pull-down' style="margin-top: 50px">
                <div class="col-md-12">
                    <div>Last deployed by <b>{{ $last->server->name }}</b>
                    on {{ $project->history->last()->created_at->toDayDateTimeString() }}</div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
