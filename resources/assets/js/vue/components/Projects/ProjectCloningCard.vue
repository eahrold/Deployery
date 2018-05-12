<template>
<transition name='fade'>
    <form-card v-if='showPanel' >
        <div slot='header'>
            <i v-if='status.cloning' class="fa fa-spinner fa-spin fa-fw"></i> {{ header }}
        </div>

        <div>Status: {{ message }}</div>
        <ul v-if='showErrors' class='list-unstyled'>
            <li v-for='error in status.errors'>
                {{ error }}
            </li>
        </ul>
        <div v-if='!status.cloning' class='btn btn-warning' @click='reclone'>Attempt Reclone</div>

    </form-card>
</transition>
</template>

<script>
    export default {
        name: "project-cloning-card",

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
                return _.get(this.status, 'message', false) ||
                    this.status.cloningError ? "Cloning Failed" : "Repo Clone in progress..."
                ;
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