<template>
    <div class="row align-items-center">
        <div class="col">
            <router-link :to='{name: "projects.servers.form", params:{id: server.id}}'>
                {{ server.name }}
            </router-link>
        </div>

        <div class="col">
            {{ server.hostname }}
        </div>

        <div class="col">
            {{ server.branch }}
        </div>

        <div class="col">
            {{ server.deployment_path }}
        </div>

        <div class="col d-flex justify-content-end align-items-center">
            <span class="mr-2">
                <a class="open-user-sshkey-modal"
                    data-toggle="modal"
                    :data-host="server.hostname"
                    :data-user="server.username"
                    data-target="#serverPubKey">
                    <i class="fa fa-key" aria-hidden="true"></i>
                </a>
            </span>

            <span class="mr-2">
                <i v-if='server.is_deploying' class="fa fa-spinner fa-spin" aria-hidden="true"></i>
                <router-link v-else :to='{name: "projects.servers.deploy", params:{id: server.id}, query: {server_name: server.name}}'>
                    <i class="fa fa-cloud-download" aria-hidden="true"></i>
                </router-link>
            </span>

            <span class="mr-2">
                <div id='status-indicator'
                    :class="statusClass">
                </div>
            </span>

            <span class="mr-2 clickable">
                <i :id="statusId"
                   class="fa fa-refresh"
                   aria-hidden="true"
                   :class='{"fa-spin" : isTesting}'
                   @click="test">
                </i>
            </span>
        </div>

    </div>
</template>

<!-- <tr v-for='server in servers'>
    <td >
        <router-link :to='{name: "projects.servers.form", params:{id: server.id}}'>
            {{ server.name }}
        </router-link>
    </td>
    <td>{{ server.hostname }}</td>
    <td class='visible-md visible-lg'>{{ server.branch }}</td>
    <td class='visible-sm visible-md visible-lg'>{{ server.deployment_path }}</td>
    <td class='crunch center visible-sm visible-md visible-lg'>
        <a class="open-user-sshkey-modal"
           data-toggle="modal"
           :data-host="server.hostname"
           :data-user="server.username"
           data-target="#serverPubKey">
            <i class="fa fa-key" aria-hidden="true"></i>
        </a>
    </td>

    <td v-if='server.is_deploying' class='crunch center'>
        <i class="fa fa-spinner fa-spin" aria-hidden="true"></i>
    </td>
    <td v-else class='crunch center'>
        <router-link :to='{name: "projects.servers.deploy", params:{id: server.id}, query: {server_name: server.name}}'>
            <i class="fa fa-cloud-download" aria-hidden="true"></i>
        </router-link>
    </td>

    <td class='crunch center'>
        <div id='status-indicator'
            :class="[statusClass(server)]">
        </div>
    </td>
    <td class='crunch center'>
        <i :id="'server_status_'+server.id"
           class="fa fa-refresh"
           aria-hidden="true"
           :class='{"fa-spin" : testing === server.id}'
           @click="test(server)">
        </i>
    </td>
</tr> -->
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
        server: {
            type: Object,
            required: true
        },

        testing: {
            type: [Number, Boolean],
            required: false,
        }
    },

    computed: {
        statusId() {
            return `server_status_${this.server.id}`
        },

        isTesting() {
            return this.server.id === this.testing
        },

        statusClass(){
            var c = 'status-';
            switch (this.server.successfully_connected) {
                case 0:
                    c +='yellow';
                    break;
                case 1:
                    c +='green';
                    break;
                case -1:
                default:
                    c +='red'
            }
            return c;
        },
    },

    methods: {
        test(){
            this.$emit('test', this.server)
        },
    }
}
</script>

<style scoped lang="scss"></style>
