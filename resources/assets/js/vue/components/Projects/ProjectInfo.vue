<template>
<div class="col-md-12">
    <div v-if='project.id' class="panel panel-default">
        <div class="pannel-nav navbar navbar-default navbar-static">
            <div class='nav navbar-nav navbar-left'>
                {{ project.name }} Info
            </div>
            <ul class='nav navbar-nav navbar-right' v-if='project.servers && project.servers.length'>
                <li>
                    <i class="fa fa-spinner fa-spin fa-fw" v-if="deployment.deploying"></i></li>
                <li>
                <deployments :v-show='!loading'
                             :project-id='project.id'
                             :servers='project.servers'
                             :progress='deployment.progress'
                             :deploying='deployment.deploying'
                             :messages='deployment.messages'>
                </deployments>
                </li>
            </ul>
        </div>

        <div class='panel-body'>
            <div class='row'>
                <div class='col-sm-4'><b>Repository</b></div>
                <div class='col-sm-8 text-right'>
                    <i v-if='loading' class="fa fa-spinner fa-spin"></i>
                    <code v-else>{{ project.repo }}</code>
                </div>
            </div>

            <div class='row'>
                <div class='col-sm-4'><b>Default Branch</b></div>
                <div class='col-sm-8 text-right'>
                    <i v-if='loading' class="fa fa-spinner fa-spin"></i>
                    <code v-else>{{ project.branch }}</code>
                </div>
            </div>

            <div class='row'>
                <div class='col-sm-4'><b>Repository Size</b></div>
                <div class='col-sm-8 text-right'>
                    <i v-if='loading' class="fa fa-spinner fa-spin"></i>
                    <code v-else>{{ repoSize }}</code>
                </div>
            </div>

            <div class='row'>
                <div class='col-sm-4'><b>Last Deployed</b></div>
                <div class='col-sm-8 text-right'>
                    <i v-if='loading' class="fa fa-spinner fa-spin"></i>
                    <code v-else>{{ lastDeployedString }}</code>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script>

export default {
    props : [
        'project',
        'deployment',
        'loading'
    ],

    computed : {
        info() {
            return this.$parent.info;
        },

        repoSize () {
            return _.get(this.info, 'repo.size', '0MB');
        },

        lastDeployedString () {
            if (this.info) {
                var lastDate = _.get(this.info, 'deployments.last.date', '???');
                var date = this.localTime(lastDate);
                if(date) {
                    return date;
                }
                return lastDate;
            }
        },

        endpoint () {
            return "/api/projects/" + this.$route.params.project_id + '/info';
        }
    }
}
</script>