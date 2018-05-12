<template>
    <form-card>
        <div slot='header'>Project Info</div>
        <div class='row'>
            <div class='col-sm-4'><b>Repository</b></div>
            <div class='col-sm-8 text-right'>
                <i v-if='loading' class="fa fa-spinner fa-spin"></i>
                <code v-else>{{ project.repo }}</code>
            </div>
        </div>

        <div class='row'>
            <div class='col-sm-4'><b>Default Branch</b></div>
            <div class='col-sm-8 text-right'>
                <i v-if='loading' class="fa fa-spinner fa-spin"></i>
                <code v-else>{{ project.branch }}</code>
            </div>
        </div>

        <div class='row'>
            <div class='col-sm-4'><b>Repository Size</b></div>
            <div class='col-sm-8 text-right'>
                <i v-if='loading' class="fa fa-spinner fa-spin"></i>
                <code v-else>{{ repoSize }}</code>
            </div>
        </div>

        <div class='row'>
            <div class='col-sm-4'><b>Last Deployed</b></div>
            <div class='col-sm-8 text-right'>
                <i v-if='loading' class="fa fa-spinner fa-spin"></i>
                <code v-else>{{ lastDeployedString }}</code>
            </div>
        </div>
    </form-card>
</template>

<script type="text/javascript">

import _ from 'lodash'

export default {
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
        loading: {},
        project: {},
        info: {},
    },

    //----------------------------------------------------------
    // Local State
    //-------------------------------------------------------
    data() {
        return {}
    },

    computed: {
        repoSize () {
            return _.get(this.info, 'repo.size', '0MB');
        },

        lastDeployedString () {
            if (this.info) {
                var lastDate = _.get(this.info, 'deployments.last.date', '???');
                var date = this.localTime(lastDate);
                if(date) {
                    return date;
                }
                return lastDate;
            }
        },
    },

    //----------------------------------------------------------
    // Events
    //-------------------------------------------------------
    // watch: {},
    // mounted() {},
    // beforeDestroy() { /* dealloc anything you need to here*/ },

    //----------------------------------------------------------
    // Non-Reactive Properties
    //-------------------------------------------------------
    methods: {

    },
}
</script>

<style scoped lang="scss"></style>
