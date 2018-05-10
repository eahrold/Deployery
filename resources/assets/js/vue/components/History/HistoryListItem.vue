<template>
    <div class="row justify-content-start align-items-center" :class="{clickable: !!history}" @click='open'>
        <div class="col text-nowrap">
            {{ history ? history.server.name : "Server" }}
        </div>

        <div class="col text-nowrap text-left d-none d-md-block">
            {{ history ? history.from_commit : "From" }}
        </div>

        <div class="col text-nowrap text-left d-none d-md-block">
            {{ history ? history.to_commit : "To" }}
        </div>

        <div class="col text-nowrap text-left d-none d-md-block">
            {{ history ?  history.user_name : "User" }}
        </div>

        <div class="col text-nowrap text-right">
            {{ history ? localTime(history.created_at) : "Deployed" }}
        </div>

    </div>
</template>

<script type="text/javascript">

import _ from 'lodash'

export default {
    name: 'history-list-item',

    //----------------------------------------------------------
    // Template Dependencies
    //-------------------------------------------------------
    // components: {},
    // directives: {},
    // filters: {},

    //----------------------------------------------------------
    // Composition
    //-------------------------------------------------------
    mixins: [],
    props: {
        history: {
            type: [Object, Boolean],
            required: true
        },
    },

    methods: {
        open(history) {
            this.$router.push({name: 'projects.history.details', params: {id: this.history.id}})
        }
    }
}
</script>

<style scoped lang="scss"></style>
