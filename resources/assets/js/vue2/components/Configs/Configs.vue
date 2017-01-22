<template>
    <div>
        <table class='table'>
            <thead>
                <th>Path</th>
                <th class='crunch'>
                    <a data-toggle="modal"
                        data-target="#configForm">
                        <i class="fa fa-plus-circle" aria-hidden="true"></i>
                    </a>
                </th>
            </thead>
            <tbody>
                <tr v-for="config in configs">
                    <td>
                        <a data-toggle="modal"
                           :data-model-id="config.id"
                           data-target="#configForm">
                            {{ config.path }}
                        </a>
                    </td>
                    <td>
                        <trash-button type='configs' :object='config'></trash-button>
                    </td>
                </tr>
            </tbody>
        </table>
        <config-form :endpoint='apiEndpoint'></config-form>
    <div>
</template>


<script>
    Vue.component('config-form', require('./ConfigForm.vue'));

    export default {
        props: [ 'configs', 'projectId' ],

        computed: {
            apiEndpoint(){
                return '/api/projects/' + this.projectId +'/configs';
            }
        }
    }
</script>