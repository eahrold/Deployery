<template>
    <div class="modal fade" id="serverForm" role="dialog">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    {{ heading }}
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class='form-group' v-if='model'>

                        <div class='panel panel-default'>
                            <div class="panel-heading">Host Settings</div>
                            <div class='panel-body'>
                                <form-text v-model='model.name' :errors='errors' property="name"></form-text>

                                <form-text v-model='model.hostname' :errors='errors' property="hostname"></form-text>

                                <form-selectize v-model='model.protocol' :errors='errors' :nullable='false' :search='false' property="protocol" :options='protocols'></form-selectize>

                                <form-number v-model='model.port' :placeholder='port' :errors='errors' property="port"></form-number>
                            </div>
                        </div>

                        <div class='panel panel-default'>
                            <div class="panel-heading">Credentials</div>
                            <div class='panel-body'>
                                <form-text v-model='model.username' :errors='errors' property="username"></form-text>
                                <form-password v-model='model.password' :errors='errors' property="password"></form-password>
                                <form-checkbox v-model='model.use_ssh_key' :errors='errors' label='Use SSH Key' property="use_ssh_key"></form-checkbox>
                            </div>
                        </div>

                        <div class='panel panel-default'>
                            <div class="panel-heading">Deployment Info</div>
                            <div class='panel-body'>
                                <form-text v-model='model.deployment_path' :errors='errors' property="deployment_path"></form-text>
                                <form-selectize v-model='model.branch' :errors='errors' :nullable='false' :search='false' property="branch" :options='branches'></form-selectize>
                                <form-text v-model='model.environment' :errors='errors' property="environment"></form-text>
                                <form-text v-model='model.sub_directory' :errors='errors' property="sub_directory"></form-text>
                            </div>
                        </div>

                        <div class='panel panel-default'>
                            <div class="panel-heading">Web Hooks</div>
                            <div class='panel-body'>
                                <form-checkbox v-model='model.autodeploy' :errors='errors' property="autodeploy"></form-checkbox>
                            </div>
                        </div>


                        <div class='panel panel-default'>
                            <div class="panel-heading">Notifications</div>
                            <div class='panel-body'>
                                <form-checkbox v-model='model.send_slack_messages' :errors='errors' property="send_slack_messages"></form-checkbox>
                                <form-text v-model='model.slack_webhook_url' :errors='errors' property="slack_webhook_url"></form-text>
                            </div>
                        </div>
                    </div>
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