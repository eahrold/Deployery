export default {
    props: [ 'projectId', 'servers' , 'messages', 'deploying'],
    http: { headers: globalHeaders },

    data() {
        return {
            currentServer: {},
            primary_status: null,
            fromCommit: '',
            toCommit: '',
            avaliableFromCommits: [],
            avaliableCommits: [],
            loading: true
        }
    },


    ready() {
        console.log('deployment is ready');
        this.avaliableFromCommits.push({'hash': 0, 'message': 'Beginning of time'});
        // this.toCommit = this.avaliableCommits[0].hash;
    },


    computed: {
        complete(){
            return !this.deploying;
        },
        hasErrors(){
            return this.errors.length;
        },
        apiEndpoint(){
            return '/api/projects/'+this.projectId+'/servers/'+this.currentServer.id;
        }
    },


    filters: {
        hashMessage(hash){
            if(hash === "0")return "Deploy the entire repo";

            for(var i = 0; i < this.avaliableCommits.length; i++){
                if(this.avaliableCommits[i].hash == hash){
                    return this.avaliableCommits[i].message;
                }
            }
            return 'Unknown commit message';
        }
    },


    methods: {
        /**
         * Set the current server
         * @param object    server server
         */
        setServer(server){
            this.currentServer = server;
        },

        getCommitDetails(server){
            this.currentServer = server;
            this.loading = true;

            this.$http.get(this.apiEndpoint+'/commit-details')
                .then(response => {
                    this.avaliableCommits = response.data.avaliable_commits;
                    this.fromCommit = response.data.last_deployed_commit;
                    this.toCommit = response.data.avaliable_commits[0].hash;
                    this.loading = false
                });
        },


        /**
         * Start Deployment
         */
        beginDeployment(){
            console.log('Deployment began...');
            this.$dispatch('deployment-began', this.currentServer);
            // Something here...
        },


        deploy(){
            var endpoint = this.apiEndpoint+'/deploy';
            var data = {
                'from': this.fromCommit,
                'to': this.toCommit
            };
            this.$http.post(endpoint, data)
                .then(response => {
                    this.beginDeployment();
                },
                response => {
                    Alerter.error(response.data.message);
            });
        },
    }
}
