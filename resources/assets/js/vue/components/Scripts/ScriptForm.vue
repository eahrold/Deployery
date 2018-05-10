<template>
<form-modal @close='close'>
    <template slot='header'>
        <b>{{ heading }}</b>
    </template>

    <!-- Modal Body -->
    <div slot="body" :class="{loading: loading}">
        <form-section heading='Install Scripts'>

            <div class='row'>
                <div class='col-md-9'>
                    <form-text v-model='model.description' :errors='errors' property="description"></form-text>
                    <form-textarea v-model='model.script' :rows='20' :errors='errors' property="script"></form-textarea>
                </div>
                <div class='col-md-3'>
                    <h4>Script Variables</h4>
                    <div class='mb-2'>You can substitute the following variables in your script</div>

                    <div class="form-group" v-for='(description, key) in parsables'>
                        <code class='variable'>{{ key }}</code>
                        <div class='help-text'>{{ description }}</div>
                    </div>
                </div>
            </div>
        </form-section>

        <form-section heading='Deployment'>
                <form-checkbox v-model='model.run_pre_deploy' property='run_pre_deploy' label="Run before deployment"></form-checkbox>
                <form-checkbox v-model='model.stop_on_failure' property='stop_on_failure' label="Stop on failure"></form-checkbox>

                <form-select
                    v-model='model.on_deployment'
                    :label='false'
                    :nullable='false'
                    property='on_deployment'
                    :options='deployments' >
                </form-select>
        </form-section>

        <form-section heading='Servers'>
            <b>Run on these servers</b>
            <div v-for='(name, key) in servers' :key='key'>
                <input type="checkbox" :id="key" :value="key" v-model="model.server_ids">
                <label :for="key">{{ name }}</label>
            </div>

            <b>Availability</b>
            <form-checkbox v-model='model.available_to_all_servers' property='available_to_all_servers' label="Make available to all servers?"></form-checkbox>
            <form-checkbox v-model='model.available_for_one_off' property='available_for_one_off' label="Make available as one-off?"></form-checkbox>
        </form-section>
    </div>
    <template slot='footer'>
        <form-save-button :saving='saving' :is-dirty='isDirty' @save='save'></form-save-button>
    </template>
</form-modal>
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