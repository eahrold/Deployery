<template src="./Project.html"></template>

<style>
.btn.btn-loading {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 60px;
    opacity: .5;
    z-index: 1000;
    line-height: 4em;
    vertical-align:middle;
    border: none;
    background-color: #96a8ad;
}
</style>

<script>

import { EchoListener } from './mixins/EchoListener';

Vue.component('project-info', require('./ProjectInfo.vue'));
Vue.component('project-cloning', require('./ProjectCloning.vue'));

export default {
    mixins: [ EchoListener ],

    data () {
        return {
            project: {},
            history: [],

            errors: [],
            formErrors: {},
            deployingServer: null,
            confirm: null,
            viewers: [],

            loading: true,
            saving: false,
            deleting: false,
            info: null,

            status: {
                cloning: false,
                cloningError: false,
                message: "",
                errors: []
            },

            deployment: {
                deploying: false,
                messages: [],
                progress: 0,
                errors: [],
                server_name: null,
                server_id: null,
            }
        }
    },

    mounted () {
        this.load();
        this.listen();
    },

    computed : {

        endpoint () {
            return '/api/projects/'+ this.$route.params.project_id;
        },

        loaded () {
            return !this.loading;
        },

        hasServers () {
            return this.hasProp('servers');
        },

        hasHistory () {
            return this.hasProp('history');
        },

        hasConfig () {
            return this.hasProp('configs');
        },

        /**
         * Return the id of the server that is deploying
         *
         * @return number   id of the server
         */
        deployingServerId(){
            return _.get(this.deployingServer, 'id');
        },

        /**
         * Return the id of the server that is deploying
         *
         * @return string   name of the server
         */
        deployingServerName(){
            return _.get(this.deployingServer, 'name');
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

    watch: {
        $route (newRoute, oldRoute) {
            if (newRoute.params.project_id !== oldRoute.params.project_id) {
                this.removeEchoListener(oldRoute)
                    .addEchoListeners();
                this.load();
                bus.$emit('project-refresh-info');
            }
        }
    },

    methods : {
        isActive(type) {
            return this.$route.name === type;
        },

        load () {
            this.loadProject();
            this.loadHistory();
            this.loadInfo();
        },

        loadProject() {
            this.loading = true;
            this.$http.get(this.endpoint).then(
                (response)=>{
                    this.loading = false;
                    this.project = response.data.data;
                },
                (response)=>{
                    this.loading = false;
                    this.$alerter.error(response.data.message)
                    console.error('Error getting project', response, this.endpoint);
            });
        },

        loadHistory() {
            this.$http.get(this.endpoint + '/history').then((response)=>{
                this.history = response.data.data;
            }, (response)=>{
                console.error("error", response);
            });
        },

        loadInfo() {
            this.$http.get(this.endpoint + '/info').then((response)=>{
                this.info = response.data;
            });
        },

        listen () {
            bus.$on('delete-project-item', this.deleteDataFromProject);
            bus.$on('add-project-item', this.appendProjectData);

            bus.$on('deployment-began', this.deploymentBegan);
            bus.$on('project-info', this.updateInfo);

            echo.private('project.'+ this.$route.params.project_id)
                .listen('HistoryCreatedEvent', this.handleHistoryCreated);
        },

        handleHistoryCreated(data){
            this.history.unshift(data.history);
        },

        updateInfo (info) {
            this.info = info;
            console.log("updating info", info);
            this.deployment.deploying = info.status.is_deploying;
            this.status.cloning = info.status.is_cloning;
            this.status.cloningError = info.status.clone_failed;
        },

        deploymentBegan (server) {
            this.deployingServer = server;
            this.handleDeployStarted({
                server: server,
                message: "Deployment began on " + server.name
            }, true);
        },

        hasProp (prop) {
            return Boolean(_.get(this.project, prop, []).length);
        },

        /**
         * Appends data to a related project array
         *
         * @param  object    object    object to be appended
         * @param  string    type     the array key / relationship
         * @param  bool      beginning  sho
         *
         */
        appendProjectData(object, type){
            if(type && this.project[type]){
                var idx = _.findIndex(this.project[type], ['id', object.id]);
                if (idx !== -1) {
                    console.log('replacing object', type);
                    this.$set(this.project[type], idx, object);
                } else {
                    console.log('adding object', type);
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
            this.project[type] = _.filter(this.project[type], (item)=>{
                return item.id !== object.id;
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
                var endpoint = this.endpoint+'/'+type+'/'+object.id;
                this.$http.delete(endpoint).then(
                    (response) => {
                        this.removeProjectData(object, type);
                    },
                    (response) => {
                        console.error('[Error Deleting '+type+' ]', response);
                });
            }
        },

        cloneRepo(){
            var endpoint = this.endpoint+'/clone-repo';
            this.status.cloningError = false;
            this.status.cloning = true;
            this.$http.post(endpoint).then(
                (response) => {
                    this.$alerter.success(response.data.message)
                },
                (response) => {
                    this.status.false = true;
                    this.$alerter.error(response.data.message)
            });
        },
    }
}
</script>