<template>
<div class="sk-wave sk-anywhere sk-sm">
    <!-- <div v-for='n in 5' class="sk-rect" :class='`sk-rect${n}`'></div> -->
    <div class="sk-rect sk-rect1"></div>
    <div class="sk-rect sk-rect2"></div>
    <div class="sk-rect sk-rect3"></div>
    <div class="sk-rect sk-rect4"></div>
    <div class="sk-rect sk-rect5"></div>
</div>
</template>

<script type="text/javascript">

import _ from 'lodash'

export default {
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
    props: {},

    //----------------------------------------------------------
    // Local State
    //-------------------------------------------------------
    data() {
        return {}
    },

    computed: {

    },

    //----------------------------------------------------------
    // Events
    //-------------------------------------------------------
    // watch: {},
    // mounted() {},
    // beforeDestroy() { /* dealloc anything you need to here*/ },

    //----------------------------------------------------------
    // Non-Reactive Properties
    //-------------------------------------------------------
    methods: {

    },
}
</script>

<style scoped lang="scss">
@import '../../../../sass/spinkit/spinkit';
</style>
