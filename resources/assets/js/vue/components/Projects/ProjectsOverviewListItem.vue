<template>
    <div class="row align-items-center">
        <div class="col">
        <router-link :to="{ name: 'projects.info', params: { project_id: project.id }}">
            {{ project.name }}
        </router-link>
        </div>
        <div class="col d-flex justify-content-end align-items-center">
            <small class="help">{{ lastDeployed }}</small>
            <router-link class='btn btn-info ml-3' :to="{ name: 'projects.info', params: { project_id: project.id }}">
                Deploy
            </router-link>
        </div>
    </div>
</template>

<!--             <table class='table table-hover'>
                <thead>
                    <th>Name</th>
                    <th>Last Deployed</th>
                    <th>Servers</th>
                </thead>
                <tbody>
                    <tr v-for='project in projects'>
                        <td>
                            <router-link :to="{ name: 'projects.info', params: { project_id: project.id }}">
                                {{ project.name }}
                            </router-link>
                        </td>
                        <td>
                            {{ lastDeployed(project) }}
                        </td>
                        <td>{{ servers(project).length }}</td>
                    </tr>
                </tbody>
            </table> -->

<script type="text/javascript">

import _ from 'lodash'

export default {
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
                return `Last Deployed ${this.timeFromNow(history.created_at)} to ${_.get(history, 'server.name', "Unknown")}`
            }
            return "Never Deployed";
        }
    }
}
</script>

<style scoped lang="scss"></style>
