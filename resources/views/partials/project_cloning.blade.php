<div class="col-md-12 tab-content"
     v-if='!project.repo_exists || status.cloning' v-cloak>

    <div class="panel panel-default">
        <div class="pannel-nav navbar navbar-default navbar-static">
            <div class='nav navbar-nav navbar-left'>
                @{{ status.cloning ? "Repo is cloning" : "There was an problem cloning the repo" }}
            </div>
            <div class='nav navbar-nav navbar-right'>
                @include('includes.user_pub_key_modal')
            </div>
        </div>
        <div class='panel-body'>
            <div class='form-group' style='height: 50px;'>
                @{{ status.message || "Check your settings and try again. If the repo is private you may need to add your ssh key to the repo host (i.e. GitHub/Bitbucket/etc...)" }}
            </div>

            <button class='btn btn-info'
                    :disabled='status.cloning'
                    @click='cloneRepo'>
                @{{ status.cloning ? "Cloning" : "Try Again?" }}
            </button>
        </div>
    </div>
</div>
