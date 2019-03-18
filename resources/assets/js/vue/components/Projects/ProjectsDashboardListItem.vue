<template>
    <div class="row align-items-center">
        <div class="col">
        <router-link :to="{ name: 'projects.overview', params: { project_id: project.id }}">
            {{ project.name }}
        </router-link>
        </div>
        <div class="col d-flex justify-content-end align-items-center">
            <small class="help" v-html='lastDeployed'></small>
            <router-link class='btn btn-info ml-3' :to="{ name: 'projects.overview', params: { project_id: project.id }}">
                Deploy
            </router-link>
        </div>
    </div>
</template>

<script type="text/javascript">

import _ from 'lodash'

export default {
    name: 'projects-dashboard-list-item',

    props: {
        project: {
            type: Object,
            required: true
        }
    },

    computed: {
        servers() {
            return _.get(this.project, 'servers', []);
        },

        history () {
            return _.get(this.project, 'latest_history', []);
        },

        lastDeployed () {
            const { history } = this
            if (history) {
                return `Last Deployed <b class='text-info'>${this.timeFromNow(history.created_at)}</b> to <b class='text-info'>${_.get(history, 'server.name', "Unknown")}</b>`
            }
            return "Never Deployed";
        }
    }
}
</script>

<style scoped lang="scss"></style>
