<template>
<div>
    <router-view :endpoint="$parent.endpoint"></router-view>
    <form-section v-if='history.length'>
        <div slot="header">
            History
        </div>

        <list-group :items='history'>
            <history-list-item class='font-weight-bold' slot='header' :history='false'></history-list-item>
            <template slot-scope="context">
                <history-list-item :history='context.item'></history-list-item>
            </template>
        </list-group>

        <div class="col-md-12 text-center">
            <ul class="pagination pagination-lg pager" id="histroyPager"></ul>
        </div>
    </form-section>

    <div v-else class="col justify-content-center align-items-center">
        <h4>Never been deployed</h4>
    </div>
</div>
</template>

<script>

import { mapGetters, mapState } from 'vuex'

import HistoryListItem from './HistoryListItem'

export default {
    components: {
        HistoryListItem,
    },

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