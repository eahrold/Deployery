<style>
    .modal-body.history-modal-body {
        height: 90vh;
        overflow: auto;
    }

    .history-file-panel {
        max-height: 300px;
        overflow: auto;
    }
</style>

<template>
    <div class="modal-content">

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title" v-html='historyHeader'></h4>
        </div>

        <div class="modal-body history-modal-body">
            <div v-if='loading || !history'>
                Loading History...
            </div>
            <div v-else>
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading"><b>Info</b></div>
                        <div class="panel-body history-file-panel">
                            <ul class="list-unstyled">
                                <li><b>{{ statusMessage }}</b></li>
                                <li>Deployed by: <b>{{ history.user_name }}</b></li>
                                <li>Deployed on: <b>{{ history.created_at }}</b></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div v-if='errors.length' class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading"><b>Errors</b></div>
                        <div class="panel-body history-file-panel">
                            <ul class="list-unstyled">
                                <li v-for='error in errors'>{{ error }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div v-for='(value, key) in details' class='col-md-6'>
                    <div class="panel panel-default">
                    <div class="panel-heading"><b>{{ key }}</b></div>
                        <div class="panel-body history-file-panel">
                            <ul class="list-unstyled">
                                <li v-for='file in value'>{{ file }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        history: {
            type: Object,
            default: function () {
                return { }
            }
        },
        loading: {
            type: Boolean,
        }
    },

    mounted () {
        var self = this;
        $('#history-modal').on('hidden.bs.modal', function () {
            self.$emit('close');
        });
    },

    computed: {

        details () {
            return {
                "Successful Uploads": this.successfulUploads,
                "Failed Uploads": this.failedUploads,
                "Successful Removals": this.successfulRemovals,
                "Failed Removals": this.failedUploads
            }
        },

        historyHeader () {
            if(!this.history || this.loading) {
                return "Getting details...";
            }
            return "Deployed from <b>"+ this.history.from_commit +"</b> to <b>"+ this.history.to_commit;
        },

        statusMessage () {
            var status = _.get(this.history, 'success', true);
            return  "Deployment " + (status ? "was successful" : "failed");
        },

        errors () {
            return _.get(this.history, 'details.errors', []);
        },

        successfulUploads () {
            return _.get(this.history, 'details.changes.uploaded.success',[]);
        },

        failedUploads () {
            return _.get(this.history, 'details.changes.uploaded.failed', []);
        },

        successfulRemovals () {
            return _.get(this.history, 'details.changes.removed.success', []);
        },

        failedRemovals () {
            return _.get(this.history, 'details.changes.removed.failed', []);
        }
    }
}
</script>