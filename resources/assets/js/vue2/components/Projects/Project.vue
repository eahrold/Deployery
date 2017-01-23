<template src="./Project.html"></template>

<script>

import { EchoListener } from './mixins/EchoListener';

Vue.component('project-info', require('./ProjectInfo.vue'));
Vue.component('project-cloning', require('./ProjectCloning.vue'));

export default {
    mixins: [ EchoListener ],

    data () {
        return {
            project: {},
            errors: [],
            formErrors: {},
            deployingServer: null,
            confirm: null,
            viewers: [],

            loading: true,
            saving: false,
            deleting: false,

            status: {
                cloning: false,
                cloningError: false,
                message: "",
                errors: []
            },

            deployment: {
                deploying: false,
                messages: [],
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
            return '/api/projects/'+ this.$route.params.id;
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

    methods : {
        load () {
            this.loading = true;
            this.$http.get(this.endpoint).then(
                (response)=>{
                    this.project = response.data.data;
                    this.loading = false;
                },
                (response)=>{
                    console.error('Error getting project', response, this.endpoint);
                    this.loading = false;
            });
        },

        updateInfo (info) {
            this.deployment.deploying = info.status.is_deploying;
            this.status.cloning = info.status.is_cloning;
            this.status.cloningError = info.status.clone_failed;
        },

        listen () {
            bus.$on('delete-project-item', this.deleteDataFromProject);
            bus.$on('add-project-item', this.appendProjectData);

            bus.$on('deployment-began', this.deploymentBegan);
            bus.$on('project-info', this.updateInfo);
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

        saveProject () {
            this.saving = true;
            this.$http.put(this.endpoint, this.project).then(
                (response) => {
                    this.saving = false;
                    this.formErrors = response.data.errors || {};
                    this.status.cloning = response.data.is_cloning;
                },
                (response) => {
                    this.saving = false;
                    this.formErrors = response.data.errors;
                    console.error('Error saving project', response);
            });
        },

        deleteProject () {
            this.deleting = true;
            this.$http.delete(this.endpoint).then(
                (response) => {
                    window.location = '/';
                },
                (response) => {
                    console.error('Error Deleting Project', response);
            });
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
                this.project[type].push(object);
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
                    Alerter.success(response.data.message)
                },
                (response) => {
                    this.status.false = true;
                    Alerter.error(response.data.message)
            });
        },
    }
}
</script>