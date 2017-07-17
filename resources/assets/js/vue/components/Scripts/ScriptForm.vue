<template>
    <div class="modal fade" id="scriptForm" role="dialog">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    {{ heading }}
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <template v-if='model'>
                        <div class='form-group'>
                            <div class='panel panel-default'>
                                <div class="panel-heading">Install Scripts</div>
                                <div class='panel-body'>
                                    <div class='col-md-9'>
                                        <form-text v-model='model.description' :errors='errors' property="description"></form-text>
                                        <form-textarea v-model='model.script' :rows='20' :errors='errors' property="script"></form-textarea>
                                    </div>
                                    <div class='col-md-3'>
                                        <h4>Script Variables</h4>
                                        <div>You can substitute the following variables in your script</div>
                                        <div class="form-group" v-for='(description, key) in parsables'>
                                            <div class='variable'>{{ key }}</div>
                                            <div class='description'>{{ description }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class='panel panel-default'>
                            <div class="panel-heading">Deployments</div>
                            <div class='panel-body'>
                                <form-checkbox v-model='model.run_pre_deploy' property='run_pre_deploy' label="Run before deployment"></form-checkbox>
                                <form-checkbox v-model='model.stop_on_failure' property='stop_on_failure' label="Stop on failure"></form-checkbox>

                                <form-selectize v-model='model.on_deployment' label='' :nullable='false' property='on_deployment' :options='deployments' :search='false'></form-selectize>
                            </div>
                        </div>

                        <div class='panel panel-default'>
                            <div class="panel-heading">Servers</div>
                            <div class='panel-body'>
                                <h4>Run on these servers</h4>
                                <div v-for='(name, key) in servers' :key='key'>
                                    <input type="checkbox" :id="key" :value="key" v-model="model.server_ids">
                                    <label :for="key">{{ name }}</label>
                                </div>

                                <div>
                                    <input type="checkbox" id="available_to_all_servers" v-model="model.available_to_all_servers">
                                    <label for="available_to_all_servers">Make available to all servers?</label>
                                </div>

                                <div>
                                    <input type="checkbox" id="available_for_one_off" v-model="model.available_for_one_off">
                                    <label for="available_for_one_off">Make available as one-off?</label>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="modal-footer">
                    <form-save-button :saving='saving' :is-dirty='isDirty' @save='save'></form-save-button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { form, modalForm } from "../../mixins/AdminForm.js";

    export default {
        mixins: [ form, modalForm ],

        props: {
            endpoint: {
                type: String,
                required: true
            }
        },

        methods : {
            schema () {
                return {
                    server_ids: []
                }
            },
        },

        computed : {
            type () {
                return 'scripts';
            },

            servers () {
                return _.get(this.options, 'servers', []);
            },

            deployments () {
                var d = _.get(this.options, 'deployments', [])
                return _.map(d, (key, value)=>{
                    return {text: key, value: value};
                });
            },

            parsables () {
                return _.get(this.options, 'parsables', []);
            }
        }
    }
</script>