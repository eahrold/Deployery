<script>
window.CreateProjectVue = (el, project, isDeploying, isCloning) => {
    return new Vue({
        el: el,
        data: {
            project: project,
            deployingServer: null,
            viewers: [],
            status: {
                cloning: isCloning,
                cloningError: false,
                message: "",
                errors: []
            },
            deployment: {
                deploying: isDeploying,
                messages: [],
                errors: [],
                server_name: null,
                server_id: null,
            }
        },

        ready(){
            this.listen();
        },

        computed: {
            /**
             * Return the id of the server that is deploying
             *
             * @return number   id of the server
             */
            deployingServerId(){
                if(this.deployment.deploying){
                    return this.deployingServer.id;
                }
            },

            /**
             * Return the id of the server that is deploying
             *
             * @return string   name of the server
             */
            deployingServerName(){
                if(this.deployment.deploying){
                    return this.deployingServer.name;
                }
            },

            /**
             * General overview message about deployment status
             *
             * @return string  message about deployment status
             */
            deployingStatusMessage(){
                return (this.deployingServerName || 'This Project') + ' is currently deploying.'
            },

            /*
             * Last sent deployment message.
             */
            deployingCurrentMessage() {
                if(this.deployment.deploying){
                    return _.first(this.deployment.messages);
                }
            }
        },


        methods: {
            /**
             * Subscribe to the echo events
             */
            listen() {
                console.log('Listening');

                echo.private('project.'+project.id)
                    .listen('DeploymentProgress', this.handleDeployProgress)
                    .listen('DeploymentStarted', this.handleDeployStarted)
                    .listen('DeploymentEnded', this.handleDeployEnded)

                    .listen('RepositoryCloneProgress', this.handleCloneProgress)
                    .listen('RepositoryCloneStarted', this.handleCloneStarted)
                    .listen('RepositoryCloneEnded', this.handleCloneEnded)

                    .listen('HistoryCreatedEvent', this.handleHistoyrCreated);

                echo.join('project-viewers.' + project.id)
                    .here(viewers => {
                        console.log(JSON.stringify(viewers));
                        this.viewers = viewers;
                });
            },

            handleHistoyrCreated(data){
                this.appendProjectData(data.history, 'history', true);
            },

            /**
             * Handle the RepositoryCloneStarted event message
             *
             * @param  object data event data
             */
            handleCloneStarted(data){
                this.status.cloning = true;
                this.status.message = data.message;
                this.status.errors = [];
            },

            /**
             * Handle the RepositoryCloneProgress event message
             *
             * @param  object data event data
             */
            handleCloneProgress(data){
                this.status.message = data.message;
                this.status.errors = data.errors;
            },

            /**
             * Handle the RepositoryCloneEnded event message
             *
             * @param  object data event data
             */
            handleCloneEnded(data){
                this.status.cloning = false;
                this.status.message = data.message;
                this.status.errors = data.errors;
                this.status.cloningError = !data.success;
                this.project.repo_exists = data.success;
                this.project.repo_size = data.repo_size;
            },

            /**
             * Handle the DeploymentMessage event message
             *
             * @param  object data event data
             */
            handleDeployProgress(data){
                this.errors = data.errors;
                this.deployment.messages.unshift(data.message);
            },
            /**
             * Handle the DeploymentStarted event message
             *
             * @param  object data event data
             */
            handleDeployStarted(data){
                this.deployment.messages = [data.message];
                this.deployment.server_id = data.server.id;
                this.deployment.server_name = data.server.name;
                this.deployment.deploying = true;

                if(this.deployingServer){
                    this.deployingServer.is_deploying = true;
                }
            },

            /**
             * Handle the DeploymentStarted event message
             *
             * @param  object data event data
             */
            handleDeployEnded(data){
                if(this.deployingServer){
                    this.deployingServer.is_deploying = false;
                }

                this.deployment.messages.unshift(data.message);
                this.deployment.server_id = null;
                this.deployment.server_name = null;
                this.deployment.deploying = false;
            },

            /**
             * Appends data to a related project array
             *
             * @param  object    object    object to be appended
             * @param  string    type     the array key / relationship
             * @param  bool      beginning  sho
             *
             */
            appendProjectData(object, type, beginning){
                if(this.project[type]){
                    if(beginning === true){
                        this.project[type].unshift(object);
                    } else {
                        this.project[type].push(object);
                    }
                }
            },

            /**
             * Remove data from a related project array
             *
             * @param  object    object    object to be appended
             * @param  string    type     the array key / relationship
             *
             */
            removeProjectData(object, type){
                this.project[type].$remove(object);
            },

            cloneRepo(){
                var endpoint = '/api/projects/'+this.project.id+'/clone-repo';
                this.status.cloningError = false;
                this.status.cloning = true;
                this.$http.post(endpoint).then(
                    (response) => {
                        Alerter.success(response.data.message)
                    },
                    (response) => {
                        this.status.false = true;
                        Alerter.error(response.data.message)
                    });
            },

            /**
             * Remove an item from one of the project's related classes
             *
             * @param  object object    the object to be removed
             * @param  string type      the relationship's key in the project object
             *
             */
            deleteDataFromProject(object, type){
                if(object.id && type){
                    var endpoint = '/api/projects/'+this.project.id+'/'+type+'/'+object.id;
                    this.$http.delete(endpoint).then(
                        (response) => {
                            console.log('Successfully Removed');
                            this.removeProjectData(object, type);
                        },
                        (response) => {
                            console.log('[Error Deleting '+type+' ]', response);
                        });
                }
            }
        },


        events: {
            /**
             * Item deleted event
             *
             * @param  object object   object getting deleted
             * @param  string type   [description]
             *
             */
            'delete-item': function (object, type) {
                this.deleteDataFromProject(object, type);
            },

            /**
             * Message dispached from children indicating a deployment started.
             *
             * @param  object server    Server object the deployment started on
             *
             */
            'deployment-began': function(server){
                this.deployingServer = server;
                this.deployment.errors = [];
                this.deployment.deploying = true;
                this.deployment.server_id = server.id;
                this.deployment.server_name = server.name;
                this.deployment.messages = ["Deployment began on "+ server.name ];
            }
        }
    });
}
</script>
