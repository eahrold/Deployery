<template>
    <div>
        <transition name='fade'>
        <div v-if='showPanel' class="col-md-12">
            <div class="panel panel-default">
                <div class="pannel-nav navbar navbar-default navbar-static">
                    <i v-if='status.cloning' class="fa fa-spinner fa-spin fa-fw"></i> {{ header }}
                </div>
                <div class='panel-body'>
                    <div>Status: {{ message }}</div>
                    <ul v-if='showErrors' class='list-unstyled'>
                        <li v-for='error in status.errors'>
                            {{ error }}
                        </li>
                    </ul>
                    <div v-if='!status.cloning' class='btn btn-warning' @click='reclone'>Attempt Reclone</div>
                </div>
            </div>
        </div>
        </transition>
    </div>
</template>

<script>
    export default {
        name: "project-cloning",

        props : {
            status : {
                type: Object,
                required: true
            }
        },

        computed : {
            header () {
                if(this.status.cloning) {
                    return "Cloning Repo...";
                }

                if(this.status.cloningError) {
                    return "Error Cloning Repo";
                }

                return "Cloning Status";
            },

            message () {
                return _.get(this.status, 'message', false) || 'Repo clone in progress...';
            },

            showPanel () {
                return this.status.cloning || this.status.cloningError;
            },

            showErrors () {
                return !this.status.cloning && this.status.cloningError
            },

        },

        methods : {
            reclone () {
                this.$emit('reclone');
            }
        }
    }
</script>