<template>
    <div class="modal fade" id="projectForm" role="dialog">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    {{ heading }}
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class='form-group' v-if='model'>

                        <div class='panel panel-default'>
                            <div class="panel-heading">Config</div>
                            <div class='panel-body'>
                                <form-text v-model='model.name' :errors='errors' property="name"></form-text>
                                <form-text v-model='model.repo' :errors='errors' property="repo"></form-text>
                                <form-text v-model='model.branch' :errors='errors' property="branch"></form-text>
                            </div>
                        </div>

                        <div class='panel panel-default'>
                            <div class="panel-heading">Notifications</div>
                            <div class='panel-body'>
                                <form-checkbox v-model='model.send_slack_messages' :errors='errors' property="send_slack_messages"></form-checkbox>
                                <form-text v-model='model.slack_webhook_url' :errors='errors' property="slack_webhook_url"></form-text>
                            </div>
                        </div>

                        <div class='panel panel-default'>
                            <div class="panel-heading">
                                <i class="fa fa-clipboard clipboard"
                                    aria-hidden="true"
                                    data-clipboard-action="copy"
                                    data-clipboard-target='#pubkey'>
                                </i>
                                <span>Add this key to your repo host</span>
                            </div>
                            <div class='panel-body'>
                                <code id='pubkey'>{{ userPubKey }}</code>
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
    var Clipboard = require('clipboard');

    $('.clipboard').tooltip({
        trigger: 'click',
        placement: 'bottom'
    });

    var setTooltip = (btn, message) => {
      $(btn).attr('data-original-title', message)
            .tooltip('show');
    }

    var hideTooltip = (btn) => {
      setTimeout(function() {
        $(btn).tooltip('hide');
      }, 3000);
    }

    import { form, modalForm } from "../../mixins/AdminForm.js";

    export default {
        mixins: [ form, modalForm ],

        mounted () {
            var clipboard = new Clipboard('.clipboard');

            clipboard.on('success', function(e) {
                setTooltip(e.trigger, 'Copied!');
                hideTooltip(e.trigger);
            }).on('error', function(e) {
                setTooltip(e.trigger, 'Press Ctrl-C to copy');
                hideTooltip(e.trigger);
            })
        },

        props: {
            endpoint: {
                type: String,
                required: true
            }
        },

        methods : {
            success (response) {
                window.location = '/projects/'+response.data.data.id;
            },
        },

        computed : {
            userPubKey () {
                return _.get(window.Deployery, 'userPubKey', '');
            }
        }
    }
</script>

<style>
#pubkey {
    word-wrap: break-word;
}
</style>