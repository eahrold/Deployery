/*
 * Component to display a list of servers per project.
 */
export default {
    props: ['servers', 'projectId'],
    http: {
        headers: globalHeaders
    },

    data() {
        return {
            status: {},
            testing: false,
            testing_id: null,
        }
    },

    ready() {},

    computed: {
        endpoint() {
            return '/projects/'+this.projectId+'/servers'
        },

        apiEndpoint(){
            return '/api/projects/'+this.projectId+'/servers';
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

        deploy(server){
            var endpoint = this.apiEndpoint+'/'+server.id+'/deploy';
            this.$http.post(endpoint)
                .then(response => {
                    this.beginDeployment(server);
                },
                response => {
                    Alerter.error(response.data.message);
            });
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
                    console.log('error:', response);
                    s.toggleClass('fa-spin');
                    server.successfully_connected = -1;
                    Alerter.error(response.data.message);
                }
            );
        },

        /**
         * Start Deployment
         */
        beginDeployment(server){
            server.is_deploying = true;
            console.log('deploying Server', server);
            Alerter.success('Stared deployment on '+server.name);
            this.$dispatch('deployment-began', server);
        }
    }
}
