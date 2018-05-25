<style lang='scss' scoped>
.status-line {}
</style>

<template>
<transition name='fade'>
    <form-modal v-if='showPanel'>

        <div slot='body' class='project-clone-modal'>
            <div class="d-flex justify-content-center">
                <h4><i v-if='status.cloning' class="fa fa-spinner fa-spin fa-fw"></i> {{ header }}</h4>
            </div>
            <input v-model='aRepo' class="form-control text-center" :disabled='status.cloning'>

            <p class="status-line"><code>{{ message }}</code></p>
            <ul v-if='showErrors' class='list-unstyled'>
                <li v-for='error in status.errors'>
                    {{ error }}
                </li>
            </ul>

            <transition name='fade'>
                <project-pub-key v-if='!status.cloning'></project-pub-key>
            </transition>
        </div>
        <div slot='footer' class="d-flex justify-content-center">
            <div v-if='!status.cloning' class='btn btn-warning' @click='reclone'>Attempt Reclone</div>
        </div>
    </form-modal>
</transition>
</template>

<script>
import ProjectPubKey from './ProjectPubKey'

export default {
    name: "project-cloning-card",
    components: {
        ProjectPubKey,
    },

    props : {
        status : {
            type: Object,
            required: true
        },

        repo : {
            type: String,
            required: false
        }
    },

    data() {
        return {
            aRepo: this.repo
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
            return _.get(this.status, 'message',
                this.status.cloningError ? "Cloning Failed" : "Repo Clone in progress..."
            )
        },

        showPanel () {
            return this.status.cloning || this.status.cloningError;
        },

        showErrors () {
            return !this.status.cloning && this.status.cloningError
        },

    },

    mounted() {
        this.aRepo = this.repo;
    },

    watch: {
        repo(repo) {
            this.aRepo = repo;
        }
    },

    methods : {
        reclone () {
            this.$emit('reclone', {repo: this.aRepo});
        }
    }
}
</script>