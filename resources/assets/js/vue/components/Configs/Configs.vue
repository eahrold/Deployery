<template>
<form-section>

    <span slot='header'>Configuration Files</span>
    <router-link
        v-if='!loading'
        slot='button'
        class='btn btn-info btn-small'
        :to='{name: "projects.configs.form", params:{id: "create"}}'
    >+ Add Config</router-link>


    <router-view :endpoint='apiEndpoint'></router-view>

    <list-group class='shadow' :items='configs'>
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

    mixins: [
        ProjectChildMixin
    ],

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