<template>
    <div class='panel panel-default'>
        <div class="pannel-nav navbar navbar-default navbar-static">
            <div class="nav navbar-nav navbar-left">Configuration Files</div>
            <div class="nav navbar-nav navbar-right">
                <a data-toggle="modal"
                   data-target="#configForm">
                    <i class="fa fa-plus-circle" aria-hidden="true"></i>
                </a>
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
                            <a data-toggle="modal"
                               :data-model-id="config.id"
                               data-target="#configForm">
                                {{ config.path }}
                            </a>
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
        <config-form :endpoint='apiEndpoint'></config-form>
    <div>
</template>

<script>
    Vue.component('config-form', require('./ConfigForm.vue'));

    export default {
        props: [
            'configs',
            'projectId'
        ],

        methods: {
            serverList(config) {
                if(!config || !config.servers)return;
                return config.servers.map((i)=>{
                    return i.name;
                }).join(', ');
            },
        },

        computed: {
            apiEndpoint(){
                return '/api/projects/' + this.projectId +'/configs';
            }
        }
    }
</script>