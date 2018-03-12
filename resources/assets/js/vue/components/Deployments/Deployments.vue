<template>
    <div class="dropdown">
        <button class='icon' id="deploy-dropdown" type="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-cloud-upload" aria-hidden="true"></i>
        </button>

        <ul class="dropdown-menu" aria-labelledby="deploy-dropdown">
            <li v-for='server in servers'>
                <router-link :to='serverDeployTo(server)'>
                    {{ server.name }}
                </router-link>
            </li>
        </ul>
    </div>
</template>

<script>

import { mapGetters, mapState } from 'vuex';


export default {
    props: ['servers'],

    methods: {
        serverDeployTo(server) {
            return {
                name: "projects.servers.deploy",
                params:{ id: server.id },
                query: { server_name: server.name }
            }
        }
    }
}
</script>
