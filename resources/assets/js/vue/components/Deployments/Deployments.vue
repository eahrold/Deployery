<template>
    <div class="dropdown">
        <button class='icon' id="deploy-dropdown" type="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-cloud-upload" aria-hidden="true"></i>
        </button>

        <ul class="dropdown-menu" aria-labelledby="deploy-dropdown">
            <li v-for='server in servers'>
                <a @click='openModal(server)'
                   data-toggle="modal"
                   data-target="#server-selector">{{ server.name }}
                </a>
            </li>
        </ul>
        <deployment v-if='server'
                    :server='server'
                    :project-id='projectId'
                    :messages='messages'
                    :deploying='deploying'
                    @close='closeModal'>
        </deployment>
    </div>
</template>

<script>

export default {
    props: ['servers', 'projectId', 'messages', 'deploying'],

    data () {
        return {
            server: null,
        }
    },

    methods: {
        openModal(server) {
            this.server = server;
        },

        closeModal() {
            this.server = null;
        }
    }
}
</script>
