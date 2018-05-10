<template>
<form-card>
    <router-view :endpoint='apiEndpoint'></router-view>

    <div slot='header'>
        <span>Configuration Files</span>
        <div class="pull-right">
            <router-link :to='{name: "projects.configs.form", params: {id: "create"}}'>
                <i class="fa fa-plus-circle" aria-hidden="true"></i>
            </router-link>
        </div>
    </div>

    <table class='table table-hover table-responsive-md'>
        <thead>
            <th scope='col'>Path</th>
            <th scope='col'>Servers</th>
            <th scope='col' class='text-right'></th>
        </thead>
        <tbody>
            <tr v-for="config in configs">
                <td scope='row'>
                    <router-link :to='{name: "projects.configs.form", params: {id: config.id}}'>
                        {{ config.path }}
                    </router-link>
                </td>
                <td>
                    {{ serverList(config) }}
                </td>
                <td class='text-right'>
                    <trash-button type='configs' :object='config'></trash-button>
                </td>
            </tr>
        </tbody>
    </table>
</form-card>
</template>

<script>
const _ = require('lodash')

export default {
    name: 'configs',

    props: [
        'project',
    ],

    methods: {
        serverList(config) {
            if(!config || !config.servers) return;
            return config.servers.map((i)=>{
                return i.name;
            }).join(', ');
        },
    },

    computed: {
        projectId() {
            return _.get(this.$route, 'params.project_id')
        },

        configs() {
            return _.get(this, 'project.configs', [])
        },

        apiEndpoint(){
            return '/api/projects/' + this.projectId +'/configs';
        }
    }
}
</script>