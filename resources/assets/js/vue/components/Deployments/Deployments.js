import vSelect from "vue-select"

export default {
    components: {vSelect},
    props: [ 'projectId', 'servers' , 'messages', 'deploying'],
    http: { headers: globalHeaders },

    data() {
        return {
            currentServer: {},
            primary_status: null,
            fromCommit: {hash: null, message: null},
            toCommit: {hash: null, message: null},
            deployEntireRepo: false,
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
        },
        selectCommits(){
            return _.map(this.avaliableCommits, (obj)=>{
                return {'label': obj.hash+": "+obj.message.substring(0, 60)+"...", value: obj.hash};
            });
        }
    },


    filters: {
        hashMessage(hash){
            console.log("hash", hash);
            if(!hash || hash === "0")return "Deploy the entire repo";

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
                    this.avaliableCommits = _.map(response.data.avaliable_commits, (obj)=>{
                        return { hash: obj.hash, 'label': obj.hash+": "+obj.message };
                    });

                    this.deployEntireRepo = (response.data.last_deployed_commit == null);

                    this.fromCommit = _.find(this.avaliableCommits,(obj)=>{
                        return obj.hash === response.data.last_deployed_commit;
                    }) || { hash: null,  'label': 'Never deployed' };

                    this.toCommit = _.first(this.avaliableCommits);
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
                'from': this.fromCommit.hash,
                'to': this.toCommit.hash,
                'deploy_entire_repo': this.deployEntireRepo
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
