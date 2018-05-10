<template>
    <form-card>
        <router-view :endpoint='apiEndpoint'></router-view>
        <div slot='header'>
            <span>Install Scripts</span>
            <div class="pull-right">
                <router-link :to='{name: "projects.scripts.form", params: {id: "create"}}'>
                    <i class="fa fa-plus-circle" aria-hidden="true"></i>
                </router-link>
            </div>
        </div>
        <table class='table table-hover table-responsive-md'>
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
    </form-card>
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