<template>
<div class="col">
    <router-view></router-view>

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
        <form-card class='mt-4' v-if='!loading && projects.length'>
            <div slot="header">
                Projects
                <span aria-hidden="true" class="pull-right">
                    <router-link :to="{name:'projects.create'}">
                        <i class="fa fa-plus-circle" aria-hidden="true"></i>
                    </router-link>
                </span>
            </div>

            <table class='table table-hover'>
                <thead>
                    <th>Name</th>
                    <th>Last Deployed</th>
                    <th>Servers</th>
                </thead>
                <tbody>
                    <tr v-for='project in projects'>
                        <td>
                            <router-link :to="{ name: 'projects.info', params: { project_id: project.id }}">
                                {{ project.name }}
                            </router-link>
                        </td>
                        <td>
                            {{ lastDeployed(project) }}
                        </td>
                        <td>{{ servers(project).length }}</td>
                    </tr>
                </tbody>
            </table>
        </form-card>
    </transition>
</div>
</template>

<script>

import ProjectPubKey from './ProjectPubKey'

export default {
    name: 'project-overview',
    components: {
        ProjectPubKey
    },

    mounted () {
        this.load()
    },

    data () {
        return {
            projects: [],
            loading: true,
        }
    },

    methods : {
        load () {
            this.loading = true;
            this.$http.get(this.endpoint).then((response)=>{
                this.loading = false;
                this.projects = response.data.data;
            }, ({response})=>{
                this.loading = false;
            });
        },

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

    computed: {
        endpoint(){
            return '/api/projects';
        },

        userPubKey () {
            return _.get(window.Deployery, 'userPubKey', '');
        }
    }
}
</script>