 <div class="col-md-12" v-if='status.cloning' v-cloak>
    <div class="panel panel-default">
        <div class="pannel-nav navbar navbar-default navbar-static">
            <div class='nav navbar-nav navbar-left'>
                Repo is cloning
            </div>
        </div>

        <div class='panel-body'>
            @{{ status.message }}
        </div>
    </div>
</div>
