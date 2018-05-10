<template>
    <div class="row align-items-center">
        <div class="col">
            <router-link :to='{name: "projects.configs.form", params: {id: config.id}}'>
                {{ config.path }}
            </router-link>
        </div>

        <div class="col">
            <span class="help-block">{{ serverList }}</span>
        </div>

        <div class="col d-flex justify-content-end align-items-center">
            <trash-button type='configs' :object='config'></trash-button>
        </div>
    </div>
</template>

<script type="text/javascript">

import _ from 'lodash'

export default {
    name: 'configs-list-item',

    //----------------------------------------------------------
    // Template Dependencies
    //-------------------------------------------------------
    // components: {},
    // directives: {},
    // filters: {},

    //----------------------------------------------------------
    // Composition
    //-------------------------------------------------------
    mixins: [],
    props: {
        config: {
            type: Object,
            required: true
        }
    },

    computed: {
        serverList() {
            const { config } = this
            if(!config.servers) return;
            return config.servers.map((i)=>{
                return i.name;
            }).join(', ');
        },
    }
}
</script>

<style scoped lang="scss"></style>
