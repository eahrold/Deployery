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

        <hr/>

        <div class='row'>
            <div class='col-sm-4'><b>Last Deployed</b></div>
            <div class='col-sm-8 text-right'>
                <i v-if='loading' class="fa fa-spinner fa-spin"></i>
                <code v-else>To <b>{{ lastDeployedServer }}</b> on <b>{{ lastDeployedDate }}</b></code>
            </div>
        </div>

        <div class='row'>
            <div class='col-sm-4'><b>Number of deployments</b></div>
            <div class='col-sm-8 text-right'>
                <i v-if='loading' class="fa fa-spinner fa-spin"></i>
                <code v-else>{{ info.deployments.count }}</code>
            </div>
        </div>

        <hr/>

        <div class='row'>
            <div class='col-sm-4'><b>Number of Servers</b></div>
            <div class='col-sm-8 text-right'>
                <i v-if='loading' class="fa fa-spinner fa-spin"></i>
                <code v-else>{{ project.servers.length }}</code>
            </div>
        </div>

        <div class='row'>
            <div class='col-sm-4'><b>Number of Configuration Files</b></div>
            <div class='col-sm-8 text-right'>
                <i v-if='loading' class="fa fa-spinner fa-spin"></i>
                <code v-else>{{ project.configs.length }}</code>
            </div>
        </div>

        <div class='row'>
            <div class='col-sm-4'><b>Number of Executable Scripts</b></div>
            <div class='col-sm-8 text-right'>
                <i v-if='loading' class="fa fa-spinner fa-spin"></i>
                <code v-else>{{ project.scripts.length }}</code>
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

        lastDeployedServer() {
            return _.get(this.info, 'deployments.last.server', '???')
        },

        lastDeployedDate () {
            if (this.info) {
                const rawDate = _.get(this.info, 'deployments.last.date', '???');
                const localDate = this.localTime(rawDate);
                return localDate || rawDate
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
