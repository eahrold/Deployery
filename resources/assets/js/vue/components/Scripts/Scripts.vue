<template>
    <form-section>
        <div slot='header'>
            <span>Install Scripts</span>
            <div class="pull-right">
                <router-link :to='{name: "projects.scripts.form", params: {id: "create"}}'>
                    <i class="fa fa-plus-circle" aria-hidden="true"></i>
                </router-link>
            </div>
        </div>

        <router-view :endpoint='apiEndpoint'></router-view>
        <list-group :items='scripts'>
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