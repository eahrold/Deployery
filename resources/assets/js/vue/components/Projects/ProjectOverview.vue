<template>
<form-section v-if='project.id'>
    <div slot='header'>
        {{ project.name }} Overview
    </div>

    <ul slot='button' class='nav' v-if='project.servers && project.servers.length'>
        <li class="nav-item"><i class="fa fa-spinner fa-spin fa-fw" v-if="deployment.deploying"></i></li>
        <li class="nav-item">
            <deployments :v-show='!loading'
                         :project-id='project.id'
                         :servers='project.servers'
                         :progress='deployment.progress'
                         :deploying='deployment.deploying'
                         :messages='deployment.messages'>
            </deployments>
        </li>
    </ul>

    <project-info-card class='shadow' v-bind='{project, loading, info}'></project-info-card>

</form-section>
</template>

<script>

import { mapGetters, mapState } from 'vuex';
import ProjectInfoCard from './ProjectInfoCard'

export default {
    name: 'project-overview',
    components: {
        ProjectInfoCard,
    },

    props : [
        'project',
        'loading',
        'info',
    ],

    computed : {
        ...mapState(['deployment']),
    }
}
</script>