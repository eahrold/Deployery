<template>
<div>
    <!-- Project Form -->
    <form-section header='Project Settings'>
        <form-card>
            <form-text v-model='project.name' property='name' :errors='errors' :required='true'></form-text>
            <form-text v-model='project.repo' property='repo' :errors='errors' :required='true'></form-text>
            <form-text v-model='project.branch' property='branch' :errors='errors' :required='true'></form-text>

            <form-checkbox v-model='project.send_slack_messages' property='send_slack_messages' :errors='errors'></form-checkbox>

            <form-text v-model='project.slack_webhook_url' property='slack_webhook_url' :rules='$validation.rules.url' :errors='errors'></form-text>

            <hr class="my-4"/>

            <project-pub-key></project-pub-key>

            <form-save-button class='mt-4' :disabled='$validation.fails' :saving='saving' @save='save'></form-save-button>
        </form-card>
    </form-section>
    <!-- End Project Form -->

    <!-- Delete Area -->
    <form-card class='mt-4'>

        <div class="form-group">
            <label class="control-label" for="name">Type the name of your project to delete</label>
            <input  v-model='confirm' type="text" name="name" class="form-control">
        </div>

        <button type="submit"
                @click='remove'
                class="btn btn-default btn-danger btn-block"
                :disabled='deleting || (project.name !== confirm)'>
                <i v-if='deleting' class="fa fa-spinner fa-spin"></i>Delete
        </button>
    </form-card>
    <!-- End Delete Area -->
</div>
</template>

<script type="text/javascript">
import ProjectPubKey from './ProjectPubKey'

export default {
    components: {
        ProjectPubKey,
    },

    props: {
        project: {
            type: Object
        }
    },

    data() {
        return {
            saving: false,
            deleting: false,
            errors: null,
            confirm: null,
        }
    },

    computed: {
        endpoint () {
            return '/api/projects/'+ this.$route.params.project_id;
        },
    },

    methods: {
        save () {
            this.saving = true;
            this.$http.put(this.endpoint, this.project).then(
                (response) => {
                    this.saving = false;
                    this.errors = response.data.errors;
                },
                ({response}) => {
                    this.saving = false;
                    this.errors = response.data.errors;
                    console.error('Error saving project', response);
            });
        },

        remove () {
            this.deleting = true;
            this.$http.delete(this.endpoint).then(
                (response) => {
                    window.location = '/';
                },
                ({response}) => {
                    this.$alerter.error('Error Deleting Project');
                    console.error('Error Deleting Project', response);
            });
        },
    }
}
</script>