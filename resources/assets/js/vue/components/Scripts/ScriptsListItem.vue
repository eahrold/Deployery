<template>
    <div class="row align-items-center">
        <div class="col">
            <router-link :to='{name: "projects.scripts.form", params: {id: script.id }}'>
                {{ script.description }}
            </router-link>
        </div>

        <div class="col d-flex justify-content-end align-items-center">
            <trash-button
                type='scripts'
                :object='script'>
            </trash-button>
        </div>
    </div>
</template>

<script type="text/javascript">

import _ from 'lodash'

export default {
    name: 'scripts-list-item',

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
        script: {
            type: Object,
            required: true
        }
    },
}
</script>

<style scoped lang="scss"></style>
