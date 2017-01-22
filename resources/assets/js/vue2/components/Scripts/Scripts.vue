<template>
    <div>
        <table class='table'>
            <thead>
                <th>Install Scripts</th>
                <th class='crunch'>
                    <a data-toggle="modal"
                        data-target="#scriptForm">
                        <i class="fa fa-plus-circle" aria-hidden="true"></i>
                    </a>
                </th>
            </thead>
            <tbody>
                <tr v-for='script in scripts'>
                    <td>
                        <a data-toggle="modal"
                           :data-model-id="script.id"
                           data-target="#scriptForm">
                            {{ script.description }}
                        </a>
                    </td>
                    <td>
                        <trash-button type='scripts'
                                   :object='script'>
                        </trash-button>
                    </td>
                </tr>
            </tbody>
        </table>

        <script-form :endpoint='apiEndpoint'></script-form>
    </div>
</template>

<script>
    Vue.component('script-form', require('./ScriptForm.vue'));

    export default {
        props: ['scripts', 'projectId'],

        computed: {
            apiEndpoint(){
                return '/api/projects/' + this.projectId +'/scripts';
            }
        }
    }
</script>