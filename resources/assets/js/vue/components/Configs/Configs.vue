<template>
<form-section>
    <div slot='header'>
        <span>Configuration Files</span>
        <div class="pull-right">
            <router-link :to='{name: "projects.configs.form", params: {id: "create"}}'>
                <i class="fa fa-plus-circle" aria-hidden="true"></i>
            </router-link>
        </div>
    </div>

    <router-view :endpoint='apiEndpoint'></router-view>
    <list-group :items='configs'>
        <template slot-scope="context">
            <configs-list-item :config='context.item'></configs-list-item>
        </template>
    </list-group>
</form-section>
</template>

<script>
const _ = require('lodash')
import ConfigsListItem from './ConfigsListItem'
import ProjectChildMixin from '../Projects/mixins/ProjectChildMixin'

export default {
    name: 'configs',
    components: {
        ConfigsListItem,
    },

    mixins: [ ProjectChildMixin ],

    methods: {
    },

    computed: {
        configs() {
            return _.get(this, 'project.configs', [])
        },

        apiEndpoint(){
            return `/api/projects/${this.projectId}/configs`
        }
    }
}
</script>