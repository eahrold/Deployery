<template>
 <div class="container container-lg">
    <transition name='fade'>
        <div v-if='!loading && !projects.length' class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class='row text-center'>
                        <a href='/projects/create'>
                            <h3>Add your first project</h3>
                        </a>
                        <div>
                            If you're deploying the project from a private repo, add this ssh key to the repo host.
                            <a href="#sshkey" data-toggle="collapse">
                                (Click to show)
                            </a>
                            <div id="sshkey" class="collapse">
                                <textarea class="form-control" rows="10"> Auth::user()->pubkey </textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>

    <transition name='fade'>
        <div class="col-md-12" v-if='!loading'>
            <div class="panel panel-default">
                <div class="panel-heading">
                    Projects
                    <span aria-hidden="true" class="pull-right">
                        <a data-toggle="modal"
                           data-target="#projectForm">
                            <i class="fa fa-plus-circle" aria-hidden="true"></i>
                        </a>
                    </span>
                </div>

                <div class="panel-body">
                    <table class='table'>
                        <thead>
                            <th>Name</th>
                            <th>Last Deployed</th>
                            <th class='crunch'>Servers</th>
                        </thead>
                        <tbody>
                            <tr v-for='project in projects'>
                                <td>
                                    <a :href='"/projects/"+project.id' alt='edit'>
                                        {{ project.name }}
                                    </a>
                                </td>
                                <td>
                                    {{ lastDeployed(project) }}
                                </td>
                                <td>{{ servers(project).length }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </transition>
</div>
</template>

<script>
    Vue.component('project-form', require('./ProjectForm.vue'));

    export default {

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
                }, (response)=>{
                    this.loading = false;
                });
            },

            servers (project) {
                return _.get(project, 'servers', []);
            },

            history (project) {
                return _.get(project, 'history', []);
            },

            lastDeployed (project) {
                var history =  _.first(this.history(project));
                if (history) {
                    return _.get(history, 'server.name', "") + ": on " + history.created_at;
                }
                return "Never Deployed";
            }
        },

        computed: {
            endpoint(){
                return '/api/projects';
            }
        }
    }
</script>