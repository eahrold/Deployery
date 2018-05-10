<template>
    <nav class="navbar  justify-content-center navbar-dark bg-secondary">
        <ul class="nav">
            <li v-for="(link, idx) in links" class='nav-item'>
                <router-link class='nav-item nav-link text-light' :class='{disabled: link.disabled}' :disabled='link.disabled' :to='{name: link.to}'>
                    <i class="fa" :class='link.icon' aria-hidden="true"></i>
                    <span class='d-none d-md-inline'>{{ link.text }}</span>
                </router-link>
            </li>
        </ul>
    </nav>
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
        status: {
            type: Object,
            required: true,
        },
        hasServers: {
            type: Boolean,
            required: true,
        }
    },

    //----------------------------------------------------------
    // Local State
    //-------------------------------------------------------
    data() {
        return {}
    },

    computed: {
        links() {
            const disabled = this.status.cloning
            const notReady = disabled && !this.hasServers

            return [
                { to: 'projects.info', text: 'Overview', icon: 'fa-dashboard', disabled: false },
                { to: 'projects.servers', text: 'Servers', icon: 'fa-server', disabled, },
                { to: 'projects.history', text: 'History', icon: 'fa-history', disabled: notReady,},
                { to: 'projects.configs', text: 'Configs', icon: 'fa-cogs', disabled: notReady, },
                { to: 'projects.scripts', text: 'Scripts', icon: 'fa-file-code-o', disabled: notReady, },
                { to: 'projects.details', text: 'Project Info', icon: 'fa-rocket', disabled: false },
            ]
        }
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
