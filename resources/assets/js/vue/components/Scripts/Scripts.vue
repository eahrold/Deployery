<template>
    <form-section>
        <span slot='header'>Pre/Post Deployment Scripts</span>
        <router-link slot='button' class='btn btn-info btn-small' :to='{name: "projects.scripts.form", params:{id: "create"}}'>
        + Add Script
        </router-link>

        <router-view :endpoint='apiEndpoint'></router-view>
        <list-group class='shadow' :items='scripts'>
            <template slot-scope="context">
                <scripts-list-item :script='context.item'></scripts-list-item>
            </template>
        </list-group>
    </form-section>
</template>

<script>
import  _ from 'lodash'
import ScriptsListItem from './ScriptsListItem'
import ProjectChildMixin from '../Projects/mixins/ProjectChildMixin'

export default {
    components: {
        ScriptsListItem,
    },

    mixins: [ ProjectChildMixin ],

    props: {},

    computed: {
        scripts() {
            return _.get(this, 'project.scripts', [])
        },

        apiEndpoint(){
            return `/api/projects/${this.projectId}/scripts`
        }
    }
}
</script>