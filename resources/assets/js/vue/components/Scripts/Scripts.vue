<template>
    <div class="panel panel-default">
        <div class="pannel-nav navbar navbar-default navbar-static">
            <div class="nav navbar-nav navbar-left">Install Scripts</div>
            <div class="nav navbar-nav navbar-right">
                <a data-toggle="modal"
                    data-target="#scriptForm">
                    <i class="fa fa-plus-circle" aria-hidden="true"></i>
                </a>
            </div>
        </div>
        <div class="panel-body">
            <table class='table'>
                <tbody>
                    <tr v-for='script in scripts'>
                        <td>
                            <a data-toggle="modal"
                               :data-model-id="script.id"
                               data-target="#scriptForm">
                                {{ script.description }}
                            </a>
                        </td>
                        <td class='center crunch'>
                            <trash-button type='scripts'
                                       :object='script'>
                            </trash-button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <script-form :endpoint='apiEndpoint'></script-form>
    </div>
</template>

<script>
    Vue.component('script-form', require('./ScriptForm.vue'));
    const _ = require('lodash')
    export default {
        props: ['project'],

        computed: {
            projectId() {
                return _.get(this, 'project.id')
            },

            scripts() {
                return _.get(this, 'project.scripts', [])
            },

            apiEndpoint(){
                return '/api/projects/' + this.projectId +'/scripts';
            }
        }
    }
</script>