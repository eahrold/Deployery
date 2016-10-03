 <div class="col-md-12" v-if='deployment.deploying' v-cloak>
    <div class="panel panel-default">
        <div class="pannel-nav navbar navbar-default navbar-static">
            <div class='nav navbar-nav navbar-left'>
                @{{ deployingStatusMessage }}
            </div>
        </div>

        <div class='panel-body'>
            @{{ deployingCurrentMessage }}
        </div>
    </div>
</div>

