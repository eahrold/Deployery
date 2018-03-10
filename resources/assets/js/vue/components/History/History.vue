<template>
<div>
    <div v-if='history.length' class="panel panel-default">
        <div class="panel-heading">
            History
        </div>
        <div class='panel-body'>
            <table class='table table-hover table-condensed table-responsive'>
                <thead>
                    <th>Server</th>
                    <th>Date</th>
                    <th>From</th>
                    <th>To</th>
                    <th class="visible-md visible-lg">By</th>
                </thead>
                <tbody id='historyTable'>
                    <tr v-for='h in history' @click='getHistory(h)'
                        data-toggle="modal"
                        data-target="#history-modal">
                        <td>{{ h.server.name }}</td>
                        <td>{{ localTime(h.created_at) }}</td>
                        <td>{{ h.from_commit }}</td>
                        <td>{{ h.to_commit }}</td>
                        <td class="visible-md visible-lg">{{ h.user_name }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="col-md-12 text-center">
                <ul class="pagination pagination-lg pager" id="histroyPager"></ul>
            </div>

            <div id="history-modal" class="modal fade">
                <div class="modal-dialog modal-lg modal-xl">
                    <history-modal :history='aHistory' :loading='loading' @close='closeModal'></history-modal>
                </div>
            </div>
        </div>
    </div>

    <div v-else>
        <h4>Never been deployed</h4>
    </div>
</div>
</template>

<script>

var moment = require('moment');

export default {
    components: {
        'history-modal' : require('./HistoryModal')
    },

    props: [ 'project' ],

    data () {
        return {
            aHistory: null,
            loading: true
        }
    },

    computed: {
        history() {
            return this.$parent.history;
        }
    },

    methods: {

        getHistory (history) {
            this.loading = true;
            var endpoint = this.$parent.endpoint + '/history/'+ history.id;

            this.$http.get(endpoint).then((response)=>{
                this.aHistory = response.data.data;
                this.loading = false;
            }, (response)=>{
                this.loading = false;
            });
        },

        closeModal () {
            this.aHistory = {};
        }
    },
}

</script>