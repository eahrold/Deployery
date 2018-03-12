<template>
    <div class='panel panel-default'>
        <router-view :endpoint='apiEndpoint'></router-view>

        <div class="pannel-nav navbar navbar-default navbar-static">
            <div class="nav navbar-nav navbar-left">Configuration Files</div>
            <div class="nav navbar-nav navbar-right">
                <router-link :to='{name: "projects.configs.form", params: {id: "create"}}'>
                    <i class="fa fa-plus-circle" aria-hidden="true"></i>
                </router-link>
            </div>
        </div>
        <div class='panel-body'>
            <table class='table'>
                <thead>
                    <th class='col-sm-3'>Path</th>
                    <th class='col-sm-6'>Servers</th>
                    <th class='col-sm-1 text-center'></th>
                </thead>
                <tbody>
                    <tr v-for="config in configs">
                        <td class='col-sm-3'>
                            <router-link :to='{name: "projects.configs.form", params: {id: config.id}}'>
                                {{ config.path }}
                            </router-link>
                        </td>
                        <td class='col-sm-6'>
                            {{ serverList(config) }}
                        </td>
                        <td class='col-sm-1 text-center'>
                            <trash-button type='configs' :object='config'></trash-button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
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