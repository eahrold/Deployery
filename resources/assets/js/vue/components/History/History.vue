<template>
<div>
    <router-view :endpoint="$parent.endpoint"></router-view>
    <form-card v-if='history.length'>
        <div slot="heading">
            History
        </div>

        <table class='table table-hover table-responsive-md'>
            <thead>
                <th>Server</th>
                <th>Date</th>
                <th>From</th>
                <th>To</th>
                <th class="visible-md visible-lg">By</th>
            </thead>
            <tbody id='historyTable'>
                <tr v-for='h in history' @click='open(h)'
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
    </form-card>

    <div v-else class="col justify-content-center align-items-center">
        <h4>Never been deployed</h4>
    </div>
</div>
</template>

<script>

import { mapGetters, mapState } from 'vuex';


export default {

    props: [ 'project' ],

    data () {
        return {
            aHistory: null,
            loading: true
        }
    },

    computed: {
        ...mapState(['history'])
    },

    methods: {
        open(history) {
            this.$router.push({name: 'projects.history.details', params: {id: history.id}})
        }
    }
}

</script>