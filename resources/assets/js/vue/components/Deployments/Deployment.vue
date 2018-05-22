<style lang='css' scoped>
.message-container {
    max-height: 40vh;
    overflow: scroll;
}

</style>
<template>
<form-modal @close='close'>
    <template slot="header">
        <h4 v-if='loading' class="modal-title d-flex align-items-center justify-content-start">
            <span class="pull-left">Getting Commit Data</span>
            <progress-rects class='pull-left'></progress-rects>
        </h4>
        <h4 v-else class="modal-title">
            <span>Deploy {{ serverName }}</span>
        </h4>
    </template>

    <template slot="body">
        <div id='deployment' class='deployments'>
            <div class="row commit-selection">
                <div class="col-md-12">
                    <form-panel>
                        <form-select label='Branch' v-model='currentBranch' :options='availableBranches' property='branch'>
                            <form-checkbox
                                slot='help'
                                v-if='currentBranch !== originalBranch'
                                v-model='useBranchForFutureDeployments'
                                label='Use this branch for future releases.'
                                property='use_branch_in_future'>
                            </form-checkbox>
                        </form-select>


                        <deployments-commit-toggler
                            title='From Commit'
                            :find-commit='findCommit'
                            :commit.sync='fromCommit'
                            :commits='availableFromCommits'>
                        </deployments-commit-toggler>

                        <deployments-commit-toggler
                            title='To Commit'
                            :find-commit='findCommit'
                            :commit.sync='toCommit'
                            :commits='availableToCommits'>
                        </deployments-commit-toggler>

                       <!--  <form-select
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
                        </form-select> -->

                        <div class="row d-flex align-items-center justify-content-center">
                            <div class="col">
                                <form-checkbox v-model='deployEntireRepo' property='deploy_entire_repo'></form-checkbox>
                            </div>
                            <div class="col d-flex justify-content-end">
                                <div class="btn btn-info" @click="getServerCommitDetails" :disabled='loading'>
                                    <span class="pr-1">Reload Remote Details</span>
                                    <i class="fa fa-refresh" :class='{"fa-spin": loading && loaded}'></i>
                                </div>
                            </div>
                        </div>

                        <hr />
                        <form-checkbox-group
                            v-if='availableScripts.length'
                            label='Run these scripts this time'
                            v-model='scriptIds'
                            value-key='id'
                            text-key='description'
                            property='script_ids'
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

        <hr/>

        <div class='row d-flex my-4'>
            <deployments-status-box
                class='col'
                v-for='n in [0,1,2,4,5]'
                :key='n'
                :stage='n'
                :active-stage='stage'>
            </deployments-status-box>
        </div>

        <div class="row mb-2">
            <div class="col-12">
            <div class="progress">
                <div class="progress-bar"
                    role="progressbar"
                    :aria-valuenow="progress"
                    aria-valuemin="0"
                    aria-valuemax="100"
                    :style='progressStyle'>
                    <span class="sr-only">{{ progress }}% Complete</span>
                </div>
            </div>
            </div>
        </div>

        <div class='row'>
            <div class="col-12">
                <div class='form-group message-container'>
                    <ul class='list-unstyled deploy-messages'>
                        <li v-for='(message, idx) in messages' :key='idx'>
                            <code v-html='message'></code>
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
import DeploymentsStatusBox from './DeploymentsStatusBox'
import DeploymentsCommitToggler from './DeploymentsCommitToggler'

import moment from 'moment'

export default {
    components: {
        DeploymentsStatusBox,
        DeploymentsCommitToggler,
    },

    data() {
        return {
            primary_status: null,
            fromCommit: {hash: null, message: null},
            toCommit: {hash: null, message: null},
            deployEntireRepo: false,
            lastDeployedCommit: null,
            availableFromCommits: [],
            availableToCommits: [],
            availableScripts: [],

            availableBranches: [],
            currentBranch: null,
            originalBranch: null,

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
        ...mapGetters(['deploying', 'progress', 'messages', 'stage', 'lastDeployment']),

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

    },

    beforeRouteEnter (to, from, next) {
        next(vm => {
            vm.$_AdminForm__from_route = from;
        })
    },

    mounted () {
        this.getServerCommitDetails();
        if ( ! this.deploying ) {
            this.$store.dispatch(this.actionTypes.DEPLOYMENT_RESET)
        }
    },

    watch: {
        currentBranch(branch) {
            this.getBranchCommits(branch)
        },

        lastDeployment(lastDeployment) {
            if (lastDeployment.success) {
                const newFrom = _.find(this.availableToCommits, {hash: lastDeployment.to_commit})
                if (newFrom) this.fromCommit = newFrom
            }
        }
    },

    methods: {
        findCommit(commit) {
            const { projectId, serverId } = this
            return this.$api.findCommit({commit, projectId, serverId})
        },

        getBranchCommits(branch) {
            const { projectId } = this
            this.loading = true;
            return this.$api.getBranchCommits({ branch, projectId }).then(({data})=>{
                console.log({data})
                this.availableToCommits = _(data).uniqBy('hash').orderBy(['date'],['desc']).value()
            }).catch((error)=>{
                console.log('Error Getting Branch Commits',{branch, error})
            }).then(()=>{
                this.loading = false;
            })
        },

        getServerCommitDetails(){
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
                    this.originalBranch = this.currentBranch = current_branch

                    // Append the last commit to the list of available commits
                    // in case the previous commit was from a different branch
                    const lastHash = _.get(last_deployed_commit_details, 'hash')
                    if ( !! lastHash) {
                        available_commits.unshift(last_deployed_commit_details)
                    }

                    this.deployEntireRepo = !lastHash
                    this.lastDeployedCommit = last_deployed_commit_details;

                    this.availableToCommits = _(available_commits).uniqBy('hash').orderBy(['date'],['desc']).value()


                    this.availableFromCommits = _.concat([],
                        this.availableToCommits,
                        {'hash': 0, 'message': 'Beginning of time', 'date': null, 'user': null}
                    )


                    this.toCommit = _.first(this.availableToCommits);
                    this.fromCommit = last_deployed_commit_details || { hash: null,  'label': 'Never deployed', date: null, user: null};

                    this.error = false;
                    this.loaded = true;
                }, ({response}) => {
                    var message = "There was a problem getting the commit details. "+response.data.message
                    this.$vfalert.error(message);
                    this.error = true;
                }).then(()=>{this.loading=false});
        },

        deploy(){
            if(this.error == true) {
                return this.getServerCommitDetails();
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
                    this.triggerBeginDeployment();
                },
                ({response}) => {
                    this.$vfalert.errorResponse(response);
            }).then(()=>{this.disabled=false});
        },


        /**
         * Start Deployment
         */
        triggerBeginDeployment(){
            const data = {
                server: this.server,
                message: `Deployment began on ${this.server.name}`
            };
            this.$store.dispatch(this.actionTypes.DEPLOYMENT_STARTED, {data})
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
