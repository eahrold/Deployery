<template>
<div>
    <router-view :endpoint='apiEndpoint'></router-view>
    <server-pubkey-modal :endpoint='apiEndpoint'></server-pubkey-modal>

    <div class='col-md-12 text-center' v-if='!servers.length'>
        <router-link :to='{name: "projects.servers.form", params:{id: "create"}}'>
            Add your first server
        </router-link>
    </div>

    <form-section>
        <div slot='header'>
            <span>Servers</span>
            <div class="pull-right">
                <router-link :to='{name: "projects.servers.form", params:{id: "create"}}'>
                    <i class="fa fa-plus-circle" aria-hidden="true"></i>
                </router-link>
            </div>
        </div>

        <list-group :items='servers'>
            <template slot-scope="context">
                <server-list-item :server='context.item' :testing='testing' @test='test'></server-list-item>
            </template>
        </list-group>
    </form-section>
</div>
</template>

<script>
/*
 * Component to display a list of servers per project.
 */
const _ = require('lodash');
import ServerPubkeyModal from './ServerPubkeyModal'
import ServerForm from './ServerForm'
import ServersListItem from './ServersListItem'
import ProjectChildMixin from '../Projects/mixins/ProjectChildMixin'

export default {
    components: {
        'server-pubkey-modal': ServerPubkeyModal,
        'server-form': ServerForm,
        'server-list-item' : ServersListItem,
    },

    mixins: [ ProjectChildMixin ],

    props: ['deployment'],

    data() {
        return {
            status: {},
            testing: false,
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
            return '/projects/'+this.projectId +'/servers'
        },

        apiEndpoint(){
            return '/api/projects/' + this.projectId +'/servers';
        }
    },

    methods: {
        test: function(server){
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