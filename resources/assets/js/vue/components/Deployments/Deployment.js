
export default {
    props: [ 'projectId', 'server' , 'messages', 'deploying', 'progress'],

    data() {
        return {
            primary_status: null,
            fromCommit: {hash: null, message: null},
            toCommit: {hash: null, message: null},
            deployEntireRepo: false,
            avaliableFromCommits: [],
            avaliableCommits: [],
            availableScripts: [],
            scriptIds: [],
            loading: true,
            loaded: false,
            disabled: false,
            error: false,
        }
    },


    mounted () {
        this.avaliableFromCommits.push({'hash': 0, 'message': 'Beginning of time'});
        this.getCommitDetails();
        var self = this;
        $('#deployment-modal').on('hidden.bs.modal', function () {
            self.$emit('close');
        });
        // this.toCommit = this.avaliableCommits[0].hash;
    },


    computed: {
        buttonClass() {
            return [
                this.error !== false ? 'btn-danger' :
                    this.deploying ? 'btn-success' : 'btn-primary'
            ];
        },

        buttonText() {
            if(this.loading) {
                return "Getting Commit Details";
            }

            if (this.error !== false) {
                return "Try Again"
            }

            return this.deploying ? "Deploying..." : "Deploy Now"
        },

        progressStyle() {
            return { width: this.progress + "%" }
        },

        complete () {
            return !this.deploying;
        },

        hasErrors () {
            return this.errors.length;
        },

        apiEndpoint () {
            return '/api/projects/' + this.projectId + '/servers/'+this.server.id;
        },

        selectCommits(){
            return _.map(this.avaliableCommits, (obj)=>{
                return {text: obj.label.substring(0, 60)+"...", value: obj};
            });
        }
    },

    methods: {

        getCommitDetails(){
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

                    this.availableScripts = response.data.avaliable_scripts;

                    this.toCommit = _.first(this.avaliableCommits);
                    this.loading = false;
                    this.error = false;
                    this.loaded = true;
                }, response => {
                    var msg = "There was a problem getting the commit details. "+response.data.message
                    this.$alerter.error(msg);
                    this.loading = false;
                    this.error = true;
                });
        },


        /**
         * Start Deployment
         */
        beginDeployment(){
            bus.$emit('deployment-began', this.server);
        },


        deploy(){
            if(this.error == true) {
                return this.getCommitDetails();
            }

            var endpoint = this.apiEndpoint+'/deploy';
            var data = {
                'from': this.fromCommit.hash,
                'to': this.toCommit.hash,
                'deploy_entire_repo': this.deployEntireRepo,
                'script_ids': this.scriptIds
            };
            this.disabled = true;
            this.$http.post(endpoint, data)
                .then((response) => {
                    this.beginDeployment();
                    this.disabled = false;
                },
                (response) => {
                    this.disabled = false;
                    this.$alerter.error(response.data.message);
            });
        },
    }
}
