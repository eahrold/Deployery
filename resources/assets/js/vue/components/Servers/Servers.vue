<template>
<div>
    <router-view :endpoint='apiEndpoint'></router-view>
    <div class='col-md-12 text-center' v-if='!servers.length'>
        <router-link :to='{name: "projects.servers.form", params:{id: "create"}}'>
            Add your first server
        </router-link>
    </div>

    <form-card>
        <div slot='header'>
            <span>Servers</span>
            <div class="pull-right">
                <router-link :to='{name: "projects.servers.form", params:{id: "create"}}'>
                    <i class="fa fa-plus-circle" aria-hidden="true"></i>
                </router-link>
            </div>
        </div>

        <div>
            <table class='table table-hover table-responsive-md'>
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
                    </tr>
                </tbody>
            </table>

            <server-pubkey-modal :endpoint='apiEndpoint'></server-pubkey-modal>
        </div>
    </form-card>
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
        }
    },

    computed: {
        projectId() {
            return _.get(this.$route, 'params.project_id')
        },

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
            return '/projects/'+this.projectId +'/servers'
        },

        apiEndpoint(){
            return '/api/projects/' + this.projectId +'/servers';
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
            const id = this.testing = server.id;
            server.successfully_connected = 0;

            var endpoint = `${this.apiEndpoint}/${id}/test`;

            this.$http.post(endpoint).then(
                (response) => {
                    server.successfully_connected = 1;
                },
                ({response}) => {
                    console.error('error:', response);
                    server.successfully_connected = -1;
                    this.$alerter.error(response.data.message);
                }
            ).then(()=>{this.testing=false});
        },
    }
}
</script>