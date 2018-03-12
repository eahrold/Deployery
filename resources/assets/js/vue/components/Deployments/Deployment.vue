<template>
<form-modal @close='$router.go(-1)'>
    <template slot="header">
        <h4 v-if='loading' class="modal-title">
            <div class="pull-left sk-wave sk-anywhere sk-sm">
                <!-- <div v-for='n in 5' :class='["sk-rect", "sk-rect"+n]'></div> -->
                <div class="sk-rect sk-rect1"></div>
                <div class="sk-rect sk-rect2"></div>
                <div class="sk-rect sk-rect3"></div>
                <div class="sk-rect sk-rect4"></div>
                <div class="sk-rect sk-rect5"></div>
            </div>
            <div class="pull-left"> Getting Commit Data...</div>
        </h4>
        <h4 v-else class="modal-title">Deploy {{ serverName }}</h4>
    </template>

    <template slot="body">
        <div id='deployment' class='deployments'>
            <div class="row commit-selection">
                <div class="col-md-12">
                    <form-panel>
                        <div v-if='loaded' @click="getCommitDetails" class='refresh-spinner col-md-12 text-right'>
                            <span><em>Refresh</em><i class="fa fa-refresh" :class='{"fa-spin": loading}'></i></span>
                        </div>

                        <form-select
                            v-model='fromCommit'
                            label="From Commit"
                            property='from_commit'
                            :options='selectCommits'>
                        </form-select>

                        <form-select
                            v-model='toCommit'
                            label="To Commit"
                            property='to_commit'
                            :options='selectCommits'>
                        </form-select>

                        <form-checkbox v-model='deployEntireRepo' property='deploy_entire_repo'></form-checkbox>

                        <hr/>
                        <form-checkbox-group
                            v-if='availableScripts.length'
                            label='Run these scripts this time'
                            v-model='scriptIds'
                            value-key='id'
                            text-key='description'
                            property='scriptIds'
                            :options='availableScripts'>
                        </form-checkbox-group>
                    </form-panel>
                </div>
            </div>
        </div>

        <div class='row'></div>

        <div class='row'>
            <div class="col-md-12">
                <div class='btn-deploy' :class='{"loading": loading || deploying}'>
                    <button class="btn"
                            :class='buttonClass'
                            @click='deploy'
                            :disabled='disabled || deploying || loading'>
                        <i v-if='deploying' class="fa fa-spinner fa-spin"></i>
                        {{ buttonText }}
                    </button>
                </div>
            </div>
        </div>

        <div class='row'>
            <div class="col-md-12">
                <div class='form-group'>
                    <hr/>
                    <template v-if='primary_status'>
                        <h3 :class="['primary-status', { 'error': errors.length }]" >
                            {{ primary_status }}
                        </h3>
                    </template>
                </div>

                <div class="progress">
                    <div class="progress-bar" role="progressbar" :aria-valuenow="progress" aria-valuemin="0" aria-valuemax="100" :style='progressStyle'>
                        <span class="sr-only">{{ progress }}% Complete</span>
                    </div>
                </div>

                <div class='form-group message-container'>
                    <ul class='list-unstyled deploy-messages'>
                        <li v-for='message in messages' track-by="$index">
                            <h4>{{ message }}</h4>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </template>
</form-modal>
</template>
<script>

import { mapGetters, mapState } from 'vuex';

export default {

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
            server: {},
        }
    },


    mounted () {
        this.avaliableFromCommits.push({'hash': 0, 'message': 'Beginning of time'});
        this.getCommitDetails();
        if ( ! this.deploying ) {
            this.$store.dispatch(this.actionTypes.DEPLOYMENT_RESET)
        }
    },

    computed: {
        ...mapState(['actionTypes']),
        ...mapGetters(['deploying', 'progress', 'messages']),

        serverName() {
            return _.get(this.$route, 'query.server_name', "")
        },

        projectId() {
            return this.$route.params.project_id
        },

        serverId() {
            return this.$route.params.id
        },

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
            return `/api/projects/${this.projectId}/servers/${this.serverId}`
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
                    this.error = false;
                    this.loaded = true;
                }, ({response}) => {
                    var msg = "There was a problem getting the commit details. "+response.data.message
                    this.$vfalert.error(msg);
                    this.error = true;
                }).then(()=>{this.loading=false});
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

            var endpoint = `${this.apiEndpoint}/deploy`
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
                },
                ({response}) => {
                    this.$vfalert.error(response.data.message);
            }).then(()=>{this.disabled=false});
        },
    }
}

</script>
