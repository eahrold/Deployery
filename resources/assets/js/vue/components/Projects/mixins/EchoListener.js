import { types } from '../../../store'

export const EchoListener = {

    mounted () {
        this.addEchoListeners();

    },

    computed: {
        __Echo(){
            return window.Echo
        }
    },

    methods : {

         /**
         * Subscribe to the Echo events
         */
        addEchoListeners() {
            const { project_id } = this.$route.params;
            // Setup Echo listeners.
            this.__Echo.private('project.'+ project_id)
                .listen('DeploymentProgress', this.handleDeployProgress)
                .listen('DeploymentStarted', this.handleDeployStarted)
                .listen('DeploymentEnded', this.handleDeployEnded)

                .listen('RepositoryCloneStarted', this.handleCloneStarted)
                .listen('RepositoryCloneProgress', this.handleCloneProgress)
                .listen('RepositoryCloneEnded', this.handleCloneEnded)

                .listen('HistoryCreatedEvent', this.handleHistoryCreated)

            this.__Echo.join('project-viewers.' + project_id)
                .here(this.handleViewersUpdated);

            return this;
        },

         /**
         * Unsubscribe to the Echo events
         */
        removeEchoListener(route) {
            const { project_id } = route.params;
            this.__Echo.leave('project.'+project_id);

            return this;
        },

        handleHistoryCreated(data){
            const entry = data.history
            this.$store.dispatch(types.HISTORY_APPEND, { entry })
        },

        /**
         * Handle the Here event
         *
         * @param  object data event data
         */
        handleViewersUpdated(viewers) {
            this.$store.dispatch(types.VIEWERS_SET, {viewers})
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
        handleCloneProgress({message, errors}){
            // console.log("Progress", {message, errors})
            this.status = _.assign({}, this.status, {message, errors});
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
        },

        /**
         * Handle the DeploymentStarted event message
         *
         * @param  object data event data
         */
        handleDeployStarted(data, locallyTriggered){
            this.$store.dispatch(types.DEPLOYMENT_STARTED, {data})
        },

        /**
         * Handle the DeploymentStarted event message
         *
         * @param  object data event data
         */
        handleDeployEnded(data){
            this.$store.dispatch(types.DEPLOYMENT_ENDED, {data})
        },
    }
}
