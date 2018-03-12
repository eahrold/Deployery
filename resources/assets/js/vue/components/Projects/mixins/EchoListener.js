import { types } from '../../../store'

export const EchoListener = {

    mounted () {
        this.addEchoListeners();
    },

    methods : {

         /**
         * Subscribe to the echo events
         */
        addEchoListeners() {
            // Setup echo listeners.
            echo.private('project.'+this.$route.params.project_id)
                .listen('DeploymentProgress', this.handleDeployProgress)
                .listen('DeploymentStarted', this.handleDeployStarted)
                .listen('DeploymentEnded', this.handleDeployEnded)

                .listen('RepositoryCloneProgress', this.handleCloneProgress)
                .listen('RepositoryCloneStarted', this.handleCloneStarted)
                .listen('RepositoryCloneEnded', this.handleCloneEnded)

            echo.join('project-viewers.' + this.$route.params.project_id)
                .here(viewers => {
                    // console.log(JSON.stringify(viewers));
                    this.viewers = viewers;
            });
            return this;
        },

        removeEchoListener(route) {
            echo.leave('project.'+route.params.id);
            return this;
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
            bus.$emit('project-refresh-info');
        },

        /**
         * Handle the DeploymentMessage event message
         *
         * @param  object data event data
         */
        handleDeployProgress(data){
            this.$store.dispatch(types.DEPLOYMENT_PROGRESS, {data})

            this.errors = data.errors;

            if(data.progress) {
                this.deployment.progress = data.progress;
            }

            this.deployment.messages.unshift(data.message);
        },

        /**
         * Handle the DeploymentStarted event message
         *
         * @param  object data event data
         */
        handleDeployStarted(data, locallyTriggered){
            this.$store.dispatch(types.DEPLOYMENT_STARTED, {data})

            this.deployment.messages = [ data.message ];
            this.deployment.progress = 0;
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
            this.$store.dispatch(types.DEPLOYMENT_ENDED, {data})

            if(this.deployingServer){
                this.deployingServer.is_deploying = false;
            }

            this.deployment.messages.unshift(data.message);
            this.deployment.server_id = null;
            this.deployment.server_name = null;
            this.deployment.deploying = false;
            this.deployment.progress = 100;
        },
    }
}
