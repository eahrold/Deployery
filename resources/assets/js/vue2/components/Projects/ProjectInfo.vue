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
                <deployments :project-id='project.id'
                             :servers='project.servers'
                             :deploying='deployment.deploying'
                             :messages='deployment.messages'>
                </deployments>
                </li>
            </ul>
        </div>

        <div class='panel-body'>
            <div class='row'>
                <div class="col-md-12">
                    <div class='pull-left'>
                        Repository
                    </div>
                    <div class='pull-right'><code>{{ project.repo }}</code></div>
                </div>
            </div>
            <div class='row'>
                <div class="col-md-12">
                    <div class='pull-left'>Default Branch</div>
                    <div class='pull-right'><code>{{ project.branch }}</code></div>
                </div>
            </div>
            <div class='row'>
                <div class="col-md-12">
                    <div class='pull-left'>
                        Repository Size
                    </div>
                    <div class='pull-right'><code>{{ repoSize }}</code></div>
                </div>
            </div>
            <div class='row'>
                <div class="col-md-12">
                    <div class='pull-left'>
                        Last Deployed
                    </div>
                    <div class='pull-right'><code>{{ lastDeployedString }}</code></div>
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
        'deployment'
    ],

    data () {
        return {
            info : {
                deployments: {
                    last: {
                        date: '',
                        server: ''
                    },
                    count: 0
                },
                repo: {
                    size: "",
                    exists: false
                },
                status: {
                    is_cloning: false,
                    is_deploying: false,
                    clone_failed: false
                }
            }
        }
    },

    mounted () {
        this.load();
        bus.$on('project-refresh-info', this.load)
    },

    methods : {
        load () {
            this.$http.get(this.endpoint).then((response)=>{
                var info = this.info = response.data;
                bus.$emit('project-info', info);
            });
        }
    },

    computed : {
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
            return "/api/projects/" + this.$route.params.id + '/info';
        }
    }
}
</script>