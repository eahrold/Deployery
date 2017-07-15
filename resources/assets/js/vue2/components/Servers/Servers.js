/*
 * Component to display a list of servers per project.
 */
Vue.component('server-pubkey-modal', require('./ServerPubkeyModal.vue'));
Vue.component('server-form', require('./ServerForm.vue'));

export default {
    props: ['servers', 'projectId', 'deploying', 'messages', 'progress'],

    data() {
        return {
            status: {},
            testing: false,
            testing_id: null,
            server: null
        }
    },

    ready() {},

    computed: {
        endpoint() {
            return '/projects/'+this.projectId+'/servers'
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
