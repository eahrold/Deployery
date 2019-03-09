<template>
<form-modal @close='close'>
    <template slot="header">
        <b>{{ header }}</b>
    </template>
    <div slot="body" :class="{loading: loading}">
        <div class='form-group' v-if='model'>
            <form-section header='Host Settings'>
                <form-text v-model='model.name' :errors='errors' property="name" :required='true'></form-text>

                <form-text v-model='model.hostname' :errors='errors' property="hostname" :required='true'></form-text>

                <form-select v-model='model.protocol' :errors='errors' :nullable='false' :search='false' property="protocol" :options='protocols'></form-select>

                <form-number v-model='model.port' :placeholder='port' :errors='errors' property="port"></form-number>
            </form-section>

            <form-section header='Credentials'>
                <form-text v-model='model.username' :errors='errors' property="username" :required='true'></form-text>
                <form-password v-model='model.password' :errors='errors' property="password"></form-password>
                <form-checkbox v-model='model.use_ssh_key' :errors='errors' label='Use SSH Key' property="use_ssh_key"></form-checkbox>
                <textarea v-if='model.use_ssh_key' rows='8' readonly>{{ pubkey }}</textarea>
            </form-section>

            <form-section header='Deployment Info'>
                    <form-text v-model='model.deployment_path' :errors='errors' property="deployment_path"></form-text>
                    <form-select v-model='model.branch' :errors='errors' :nullable='false' :search='false' property="branch" :options='branches'></form-select>
                    <form-text v-model='model.environment' :errors='errors' property="environment"></form-text>
                    <form-text v-model='model.sub_directory' :errors='errors' property="sub_directory"></form-text>
            </form-section>

            <form-section header='Web Hooks'>
                <form-checkbox v-model='model.autodeploy' :errors='errors' property="autodeploy"></form-checkbox>
                <form-text :value='model.webhook' property='webhook' :readonly="true">
                    <template slot='addon'>
                        <a @click='resetWebhook' class="clickable">Reset</a>
                    </template>
                </form-text>
            </form-section>


            <form-section header='Notifications'>
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

        data() {
            return {
                pubkey: null,
            }
        },

        computed: {
            header() {
                return this.loading ? "Loading..." : `Editing Server ${this.model.name}`
            },

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

        watch: {
            'model.use_ssh_key' : function(use) {
                if(use) {
                    this.getPubKey()
                }
            }
        },

        methods: {
            getPubKey () {
                if (this.pubkey) return;

                this.$http.get(this.endpoint + '/pubkey').then((response)=>{
                    this.pubkey = response.data.key;
                }, ({response}) => {
                    console.error('error getting pubkey');
                });
            },

            resetWebhook() {
                let endpoint = `${this.apiEndpoint}/webhook/reset`
                this.$http.put(endpoint).then(this.saved, this.failure);
            }
        }
    }
</script>