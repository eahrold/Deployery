<template>
<form-modal @close='$router.go(-1)'>

    <template slot="header">
        <h4>Setup a new Project</h4>
    </template>

    <div slot="body" :class="{loading: loading}">
        <div class='form-group' v-if='model'>

            <form-section heading='Config'>
                <form-text v-model='model.name' :errors='errors' property="name"></form-text>
                <form-text v-model='model.repo' :errors='errors' property="repo"></form-text>
                <form-text v-model='model.branch' :errors='errors' property="branch"></form-text>
            </form-section>

            <form-section heading='Notifications'>
                <form-checkbox v-model='model.send_slack_messages' :errors='errors' property="send_slack_messages"></form-checkbox>
                <form-text v-model='model.slack_webhook_url' :errors='errors' property="slack_webhook_url"></form-text>
            </form-section>

            <project-pub-key></project-pub-key>

        </div>
    </div>

    <template slot="footer">
        <form-save-button :saving='saving' :is-dirty='isDirty' @save='save'></form-save-button>
    </template>

</form-modal>
</template>

<script>

import { form, modalForm } from "../../mixins/AdminForm.js";
import ProjectPubKey from './ProjectPubKey'

export default {
    name: 'project-form',
    components: {
        ProjectPubKey,
    },

    mixins: [ form, modalForm ],

    mounted () {},

    methods : {
        success (response) {
            window.location = '/projects/'+response.data.data.id + '/info';
        },
    },

    computed : {
        endpoint() {
            return '/api/projects'
        },
    }
}
</script>

<style scoped>
.vf-modal-container {
    max-width: 80vw;
}
</style>