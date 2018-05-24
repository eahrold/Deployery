<template>
<form-modal @close='close'>

    <template slot="header">
        <h4 class="font-weight-bold">Setup a new Project</h4>
    </template>

    <div slot="body" :class="{loading: loading}" >
        <form-section header='Config'>
            <form-text v-model='model.name' :errors='errors' property="name" :required='true'></form-text>
            <form-text v-model='model.repo' :errors='errors' property="repo" :rules='repoUrl' :required='true'></form-text>
            <form-text v-model='model.branch' :errors='errors' property="branch" :required='true'></form-text>
        </form-section>

        <form-section header='Notifications'>
            <form-checkbox v-model='model.send_slack_messages' :errors='errors' property="send_slack_messages"></form-checkbox>
            <form-text v-model='model.slack_webhook_url' :errors='errors' property="slack_webhook_url"></form-text>
        </form-section>

        <form-section>
            <project-pub-key></project-pub-key>
        </form-section>
    </div>

    <template slot="footer">
        <form-save-button
            :disabled='$validation.fails'
            :saving='saving'
            @save='save'>
        </form-save-button>
    </template>

</form-modal>
</template>

<script>

import ProjectPubKey from './ProjectPubKey'

export default {
    name: 'project-create-modal',
    components: {
        ProjectPubKey,
    },

    data() {
        return {
            errors: null,
            loading: false,
            saving: false,
            model: {
                name: null,
                repo: null,
                branch: null,
                send_slack_messages: false,
                slack_webhook_url: '',
            }
        }
    },

    mounted () {},

    methods : {
        save() {
            const { model } = this;
            this.$http.post(this.endpoint, model).then((response)=>{
                const { id } = response.data.data
                window.location = `/projects/${id}/overview`;
            }).catch((error)=>{
                this.errors = _.get(error, 'response.data.errors');
                this.$vfalert.errorResponse(error.response);
            })
        },


        repoUrl(value) {
            var regex = /^(?:git|ssh|https?|git@[-\w.]+):(\/\/)?(.*?)(\.git)(\/?|\#[-\d\w._]+?)$/;
            return regex.test(value) || "The Format is invalid";
        },

        close() {
            this.$router.go(-1)
        }
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