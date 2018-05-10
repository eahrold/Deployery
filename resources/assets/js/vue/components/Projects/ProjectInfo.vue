<template>
<div class="col-md-12">
    <form-section v-if='project.id'>
        <div slot='header'>
            {{ project.name }} Overview
            <ul class='nav pull-right' v-if='project.servers && project.servers.length'>
                <li class="nav-item"><i class="fa fa-spinner fa-spin fa-fw" v-if="deployment.deploying"></i></li>
                <li class="nav-item">
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


        <form-card>
            <div slot='header'>Project Info</div>
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
        </form-card>
    </form-section>
</div>
</template>

<script>

import { mapGetters, mapState } from 'vuex';

export default {
    name: 'project-info',

    props : [
        'project',
        'loading'
    ],

    computed : {
        ...mapState(['deployment']),

        projectId() {
            return _.get(this.$route, 'params.project_id')
        },

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
            return `/api/projects/${this.projectId}/info`
        }
    }
}
</script>