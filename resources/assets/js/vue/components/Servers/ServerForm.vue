<template>
<form-modal @close='close'>
    <template slot="header">
        <b>{{ heading }}</b>
    </template>
    <div slot="body" :class="{loading: loading}">
        <div class='form-group' v-if='model'>
            <form-section heading='Host Settings'>
                <form-text v-model='model.name' :errors='errors' property="name" :required='true'></form-text>

                <form-text v-model='model.hostname' :errors='errors' property="hostname" :required='true'></form-text>

                <form-select v-model='model.protocol' :errors='errors' :nullable='false' :search='false' property="protocol" :options='protocols'></form-select>

                <form-number v-model='model.port' :placeholder='port' :errors='errors' property="port"></form-number>
            </form-section>

            <form-section heading='Credentials'>
                <form-text v-model='model.username' :errors='errors' property="username" :required='true'></form-text>
                <form-password v-model='model.password' :errors='errors' property="password"></form-password>
                <form-checkbox v-model='model.use_ssh_key' :errors='errors' label='Use SSH Key' property="use_ssh_key"></form-checkbox>
            </form-section>

            <form-section heading='Deployment Info'>
                    <form-text v-model='model.deployment_path' :errors='errors' property="deployment_path"></form-text>
                    <form-select v-model='model.branch' :errors='errors' :nullable='false' :search='false' property="branch" :options='branches'></form-select>
                    <form-text v-model='model.environment' :errors='errors' property="environment"></form-text>
                    <form-text v-model='model.sub_directory' :errors='errors' property="sub_directory"></form-text>
            </form-section>

            <form-section heading='Web Hooks'>
                <form-checkbox v-model='model.autodeploy' :errors='errors' property="autodeploy"></form-checkbox>
                {{ model.webhook }}
            </form-section>


            <form-section heading='Notifications'>
                <form-checkbox v-model='model.send_slack_messages' :errors='errors' property="send_slack_messages"></form-checkbox>
                <form-text v-model='model.slack_webhook_url' :rules='$validation.rules.url' :errors='errors' property="slack_webhook_url"></form-text>
            </form-section>
        </div>
    </div>

    <template slot="footer">
        <form-save-button :disabled='$validation.fails' :saving='saving' :is-dirty='isDirty' @save='save'></form-save-button>
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

        computed: {
            port () {
                return _.get(this.model, 'port', 22);
            },


            branches () {
                return _.get(this.options, 'branches', []).map((branch)=>{
                    return { value: branch, text: branch }
                });
            },

            protocols () {
                return _.get(this.options, 'protocols', ['ssh','sftp']);
            },

            type () {
                return 'servers';
            },
        },
    }
</script>