<div class="col-md-12">
    <div class="panel panel-default">
        <div class="pannel-nav navbar navbar-default navbar-static">
            <div class='nav navbar-nav navbar-left'>
                @{{ project.name }} Info
            </div>
            <ul class='nav navbar-nav navbar-right' v-if='project.servers.length'>
                <li>
                    <i class="fa fa-spinner fa-spin fa-fw" v-if="deployment.deploying"></i></li>
                <li>
                <deployments :project-id='project.id'
                             :servers='project.servers'
                             :deploying='deployment.deploying'
                             :messages='deployment.messages'>
                </deployments>
                </li>
            </ul>
        </div>
        {{-- End Project Info Nav --}}
        <div class='panel-body'>
            <div class='row'>
                <div class="col-md-12">
                    <div class='pull-left'>
                        Repository
                    </div>
                    <div class='pull-right'><code>@{{ project.repo }}</code></div>
                </div>
            </div>
            <div class='row'>
                <div class="col-md-12">
                    <div class='pull-left'>Default Branch</div>
                    <div class='pull-right'><code>@{{ project.branch }}</code></div>
                </div>
            </div>
            <div class='row'>
                <div class="col-md-12">
                    <div class='pull-left'>
                        Repository Size
                    </div>
                    <div class='pull-right'><code>@{{ project.repo_size }}</code></div>
                </div>
            </div>
        </div>
    </div>
    {{-- End Project Info Body --}}
</div>
{{-- End Project Info Panel --}}