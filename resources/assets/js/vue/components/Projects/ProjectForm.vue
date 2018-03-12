<template>
<form-modal @close='$router.go(-1)'>

    <template slot="header">
        <h4>Setup a new Project</h4>
    </template>

    <div slot="body" :class="{loading: loading}">
        <div class='form-group' v-if='model'>

            <form-panel heading='Config'>
                <form-text v-model='model.name' :errors='errors' property="name"></form-text>
                <form-text v-model='model.repo' :errors='errors' property="repo"></form-text>
                <form-text v-model='model.branch' :errors='errors' property="branch"></form-text>
            </form-panel>

            <form-panel heading='Notifications'>
                <form-checkbox v-model='model.send_slack_messages' :errors='errors' property="send_slack_messages"></form-checkbox>
                <form-text v-model='model.slack_webhook_url' :errors='errors' property="slack_webhook_url"></form-text>
            </form-panel>

            <div class='panel panel-default pubkey-wrapper'>
                <div class="panel-heading">
                    <i class="fa fa-clipboard clipboard"
                        aria-hidden="true"
                        data-clipboard-action="copy"
                        data-clipboard-target='#pubkey'>
                    </i>
                    <span>Add this key to your repo host</span>
                </div>
                <div class='panel-body'>
                    <code id='pubkey' class='pubkey'>{{ userPubKey }}</code>
                </div>
            </div>

        </div>
    </div>

    <template slot="footer">
        <form-save-button :saving='saving' :is-dirty='isDirty' @save='save'></form-save-button>
    </template>

</form-modal>
</template>

<script>
var Clipboard = require('clipboard');

import { form, modalForm } from "../../mixins/AdminForm.js";

export default {
    name: 'project-form',

    mixins: [ form, modalForm ],

    mounted () {
        var clipboard = new Clipboard('.clipboard');
        clipboard.on('success', (e)=>{
            this.setTooltip(e.trigger, 'Copied!');
            this.hideTooltip(e.trigger);
        }).on('error', (e)=>{
            this.setTooltip(e.trigger, 'Press Ctrl-C to copy');
            this.hideTooltip(e.trigger);
        })

        $('.clipboard').tooltip({
            trigger: 'click',
            placement: 'bottom'
        });
    },

    methods : {

        setTooltip(btn, message){
          $(btn).attr('data-original-title', message)
                .tooltip('show');
        },

        hideTooltip(btn){
          setTimeout(function() {
            $(btn).tooltip('hide');
          }, 3000);
        },

        success (response) {
            window.location = '/projects/'+response.data.data.id + '/info';
        },
    },

    computed : {
        endpoint() {
            return '/api/projects'
        },

        userPubKey () {
            return _.get(window.Deployery, 'userPubKey', '');
        }
    }
}
</script>

<style scoped>
.vf-modal-container {
    max-width: 80vw;
}

.pubkey-wrapper {
    max-width: 80vw;
}
.pubkey {
    word-wrap: break-word;
}
</style>