<template>
<div>
    <router-view></router-view>
    <div v-if='!this.$route.params.project_id' class="col">

        <transition name='fade'>
            <div v-if='!loading && !projects.length' class="col-md-8 offset-2 my-4">
                <form-card>
                    <router-link class='text-center' :to="{name:'projects.create'}">
                        <h5><i class="fa fa-plus-circle" aria-hidden="true"></i> Add your first Project</h5>
                    </router-link>

                    <project-pub-key></project-pub-key>

                </form-card>
            </div>
        </transition>

        <transition name='fade'>
            <form-section class='mt-4' v-if='!loading && projects.length'>
                <div slot="header">
                    Projects
                </div>

                <router-link slot='button' class='btn btn-info' :to="{name:'projects.create'}">
                    <span>+ Add a New Project</span>
                </router-link>

                <list-group class='shadow' :items='projects'>
                    <template slot-scope="context">
                        <projects-dashboard-list-item
                            :project='context.item'>
                        </projects-dashboard-list-item>
                    </template>
                </list-group>
            </form-section>
        </transition>
    </div>
</div>
</template>

<script>

import ProjectPubKey from './ProjectPubKey'
import ProjectsDashboardListItem from './ProjectsDashboardListItem'

import { mapState } from 'vuex'

export default {
    name: 'projects-dashboard',
    components: {
        ProjectsDashboardListItem,
        ProjectPubKey
    },

    mounted () {},

    data () {
        return {
            // projects: [],
            // loading: true,
        }
    },


    computed: {
        ...mapState({
            projects: 'projects',
            loading: 'proejctsLoading'
        }),

        endpoint(){
            return '/api/projects';
        },

        userPubKey () {
            return _.get(window.Deployery, 'userPubKey', '');
        }
    },

    methods : {

        servers (project) {
            return _.get(project, 'servers', []);
        },

        history (project) {
            return _.get(project, 'latest_history', []);
        },

        lastDeployed (project) {
            var history =  this.history(project);
            if (history) {
                return "To " + _.get(history, 'server.name', "Unknown") + " on " + history.created_at;
            }
            return "Never Deployed";
        }
    },
}
</script>