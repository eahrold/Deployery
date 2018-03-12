<template>
    <div class="panel panel-default">
        <div class="pannel-nav navbar navbar-default navbar-static">
            <div class="nav navbar-nav navbar-left">Install Scripts</div>
            <div class="nav navbar-nav navbar-right">
                <router-link :to='{name: "projects.scripts.form", params: {id: "create"}}'>
                    <i class="fa fa-plus-circle" aria-hidden="true"></i>
                </router-link>
            </div>
        </div>
        <div class="panel-body">
            <table class='table'>
                <tbody>
                    <tr v-for='script in scripts'>
                        <td>
                            <router-link :to='{name: "projects.scripts.form", params: {id: script.id }}'>
                                {{ script.description }}
                            </router-link>

                        </td>
                        <td class='center crunch'>
                            <trash-button type='scripts'
                                       :object='script'>
                            </trash-button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <router-view :endpoint='apiEndpoint'></router-view>
    </div>
</template>

<script>
const _ = require('lodash')
export default {
    props: [
        'project'
    ],

    computed: {
        projectId() {
            return _.get(this.$route, 'params.project_id')
        },

        scripts() {
            return _.get(this, 'project.scripts', [])
        },

        apiEndpoint(){
            return `/api/projects/${this.projectId}/scripts`
        }
    }
}
</script>