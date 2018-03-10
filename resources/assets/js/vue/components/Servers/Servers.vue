<template>
<div>
    <server-form :endpoint='apiEndpoint'></server-form>
    <div class='col-md-12 text-center' v-if='!servers.length'>
        <a data-toggle="modal"
           data-target="#serverForm">
            <i class="fa fa-plus-circle" aria-hidden="true"></i>
            Add Your first server.
        </a>
    </div>

    <div v-else class='panel panel-default panel-full'>
        <div class="pannel-nav navbar navbar-default navbar-static">
            <div class="nav navbar-nav navbar-left">Servers</div>
            <div class="nav navbar-nav navbar-right">
                <a data-toggle="modal"
                   data-target="#serverForm">
                    <i class="fa fa-plus-circle" aria-hidden="true"></i>
                </a>
            </div>
        </div>

        <div class='panel-body'>
            <table class='table table-hover table-responsive'>
                <thead>
                    <th>Name</th>
                    <th>Hostname</th>
                    <th class="visible-md visible-lg">Branch</th>
                    <th class="visible-sm visible-md visible-lg">Path</th>
                    <th class='crunch center visible-sm visible-md visible-lg'></th>
                    <th class='crunch center'></th>
                    <th class='crunch center'></th>
                    <th class='crunch center'></th>
                </thead>
                <tbody>
                    <tr v-for='server in servers'>
                        <td >
                            <a data-toggle="modal"
                               :data-model-id="server.id"
                               data-target="#serverForm">
                                {{ server.name }}
                            </a>
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
                            <a @click='openModal(server)' data-toggle="modal" data-target="#server-deployment-modal">
                                <i class="fa fa-cloud-download" aria-hidden="true"></i>
                            </a>
                        </td>

                        <td class='crunch center'>
                            <div id='status-indicator'
                                v-bind:class="[statusClass(server)]">
                            </div>
                        </td>
                        <td class='crunch center'>
                            <i :id="'server_status_'+server.id"
                               class="fa fa-refresh"
                               aria-hidden="true"
                               :class=''
                               @click="test(server)">
                            </i>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="deployments">
                <!-- Button HTML (to Trigger Modal) -->
                <!-- Modal HTML -->
                <div id="server-deployment-modal" class="modal fade">
                    <div class="modal-dialog modal-lg modal-xl">
                        <deployment v-if='server'
                                    :server='server'
                                    :project-id='project.id'
                                    :progress='progress'
                                    :messages='messages'
                                    :deploying='deploying'
                                    @close='closeModal'>
                        </deployment>
                    </div>
                </div>
            </div>
            <server-pubkey-modal :endpoint='apiEndpoint'></server-pubkey-modal>
        </div>
    </div>
</div>
</template>

<script>
/*
 * Component to display a list of servers per project.
 */
const _ = require('lodash');
import ServerPubkeyModal from './ServerPubkeyModal'
import ServerForm from './ServerForm'

export default {
    components: {
        'server-pubkey-modal': ServerPubkeyModal,
        'server-form': ServerForm
    },

    props: ['project', 'deployment'],

    data() {
        return {
            status: {},
            testing: false,
            testing_id: null,
            server: null
        }
    },

    computed: {

        servers() {
            return _.get(this, 'project.servers', []);
        },

        deploying() {
            return _.get(this, 'deployment.deploying', false);
        },

        progress() {
            return _.get(this, 'deployment.progress', 0);
        },

        messages() {
            return _.get(this, 'deployment.messages', []);
        },

        endpoint() {
            return '/projects/'+this.project.id +'/servers'
        },

        apiEndpoint(){
            return '/api/projects/' + this.project.id +'/servers';
        }
    },

    methods: {
        statusClass: function(server){
            var c = 'status-';
            switch (server.successfully_connected) {
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

        test: function(server, idx){
            this.testing = true;
            server.successfully_connected = 0;
            var id = server.id;
            var s = $("#server_status_"+id);
            s.toggleClass('fa-spin');

            var endpoint = '/api'+this.endpoint+'/'+id+'/test';
            this.$http.post(endpoint).then(
                (response) => {
                    s.toggleClass('fa-spin');
                    server.successfully_connected = 1;
                },
                (response) => {
                    console.error('error:', response);
                    s.toggleClass('fa-spin');
                    server.successfully_connected = -1;
                    this.$alerter.error(response.data.message);
                }
            );
        },

        /**
         * Start Deployment
         */
        openModal(server){
            this.server = server;
        },

        closeModal() {
            this.server = null;
        }
    }
}
</script>