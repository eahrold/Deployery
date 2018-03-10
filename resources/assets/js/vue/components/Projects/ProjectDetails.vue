<template>
<div>
    <!-- Project Form -->
    <div class='panel panel-default'>
        <div class="panel-body">
            <form-text v-model='project.name' property='name' :errors='errors'></form-text>
            <form-text v-model='project.repo' property='repo' :errors='errors'></form-text>
            <form-text v-model='project.branch' property='branch' :errors='errors'></form-text>

            <form-checkbox v-model='project.send_slack_messages' property='send_slack_messages' :errors='errors'></form-checkbox>

            <form-text v-model='project.slack_webhook_url' property='slack_webhook_url' :errors='errors'></form-text>

            <div class='pin-right projects'>
                <div class="btn-toolbar">
                    <form-save-button :saving='saving' @save='save'></form-save-button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Project Form -->

    <!-- Delete Area -->
    <div class="panel panel-default">
        <div class='panel-body'>
            <div class='row row-centered'>
                <div class='col-md-4 col-centered'>

                    <div class="form-group">
                        <label class="control-label" for="name">Type the name of your project to delete</label>
                        <input  v-model='confirm' type="text" name="name" class="form-control">
                    </div>

                    <button type="submit"
                            @click='remove'
                            class="btn btn-default btn-danger btn-delete"
                            :disabled='deleting || (project.name !== confirm)'>
                            <i v-if='deleting' class="fa fa-spinner fa-spin"></i>Delete
                    </button>

                </div>
            </div>
        </div>
    </div>
    <!-- End Delete Area -->
</div>
</template>

<script type="text/javascript">
export default {
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
                (response) => {
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
                (response) => {
                    this.$alerter.error('Error Deleting Project');
                    console.error('Error Deleting Project', response);
            });
        },
    }
}
</script>