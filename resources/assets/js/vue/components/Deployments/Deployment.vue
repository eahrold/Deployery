<style lang='css' scoped>
.message-container {
    max-height: 40vh;
    overflow: scroll;
}

</style>
<template>
<form-modal @close='close'>
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

                        <hr />
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
                        <li v-for='(message, idx) in messages' :key='idx'>
                            <h4 v-html='message'></h4>
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
import moment from 'moment'

export default {

    data() {
        return {
            primary_status: null,
            fromCommit: {hash: null, message: null},
            toCommit: {hash: null, message: null},
            deployEntireRepo: false,
            lastDeployedCommit: null,
            availableFromCommits: [],
            availableCommits: [],
            availableScripts: [],

            availableBranches: [],
            currentBranch: null,
            useBranchForFutureDeployments: false,
            scriptIds: [],
            loading: true,
            loaded: false,
            disabled: false,
            error: false,

            $_AdminForm__from_route: null,
        }
    },

    computed: {
        ...mapState(['actionTypes']),
        ...mapGetters(['deploying', 'progress', 'messages']),

        server() {
            return {
                id: this.serverId,
                name: this.serverName
            }
        },

        serverId() {
            return this.$route.params.id
        },

        serverName() {
            return _.get(this.$route, 'query.server_name', "")
        },

        projectId() {
            return this.$route.params.project_id
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
            return _.map(this.availableCommits, (commit)=>{
                const label = `${commit.hash} : ${commit.message} (${moment(commit.date).format("ll h:mm a")})`
                return {
                    text: label.substring(0, 80) + (label.length > 80 ? '...' : ""),
                    value: commit
                };
            });
        }
    },

    beforeRouteEnter (to, from, next) {
        next(vm => {
            vm.$_AdminForm__from_route = from;
        })
    },

    mounted () {
        this.availableFromCommits.push({'hash': 0, 'message': 'Beginning of time'});
        this.getCommitDetails();
        if ( ! this.deploying ) {
            this.$store.dispatch(this.actionTypes.DEPLOYMENT_RESET)
        }
    },


    methods: {

        getCommitDetails(){
            this.loading = true;

            this.$http.get(this.apiEndpoint+'/commit-details')
                .then(response => {
                    const {
                        current_branch,
                        available_branches,
                        available_scripts,
                        available_commits,
                        last_deployed_commit_details,
                    } = response.data;

                    this.availableScripts = available_scripts

                    this.availableBranches = available_branches
                    this.currentBranch = current_branch

                    // Append the last commit to the list of available commits
                    // in case the previous commit was from a different branch
                    const lastHash = _.get(last_deployed_commit_details, 'hash')
                    if ( !! lastHash) {
                        available_commits.unshift(last_deployed_commit_details)
                    }

                    this.deployEntireRepo = !lastHash
                    this.lastDeployedCommit = last_deployed_commit_details;
                    this.availableCommits = _(available_commits).uniqBy('hash').orderBy(['date'],['desc']).value()

                    this.toCommit = _.first(this.availableCommits);
                    this.fromCommit = last_deployed_commit_details || { hash: null,  'label': 'Never deployed', date: null, user: null};

                    this.error = false;
                    this.loaded = true;
                }, ({response}) => {
                    var message = "There was a problem getting the commit details. "+response.data.message
                    this.$vfalert.error(message);
                    this.error = true;
                }).then(()=>{this.loading=false});
        },


        /**
         * Start Deployment
         */
        beginDeployment(){
            const data = {
                server: this.server,
                message: `Deployment began on ${this.server.name}`
            };
            this.$store.dispatch(this.actionTypes.DEPLOYMENT_STARTED, {data})
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
                'script_ids': this.scriptIds,

                'branch': this.currentBranch,
                'use_branch_in_future': this.useBranchForFutureDeployments,
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

        close() {
            if (!_.isEmpty(this.$_AdminForm__from_route)) {
                this.$router.go(-1);
                return;
            }
            const { project_id } = this.$route.params
            this.$router.push({name: 'projects.servers', params: {project_id, }});
        }
    }
}
</script>
