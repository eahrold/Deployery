<template>
<ul class="list-group">
    <li class="list-group-item" v-if='$slots["header"]'><slot name='header'></slot></li>
    <li v-if='items' v-for='(item, idx) in items' :key='idx' class="list-group-item">
        <slot v-bind:item="item"></slot>
    </li>
</ul>
</template>

<script type="text/javascript">

export default {
    name: 'list-group',

    //----------------------------------------------------------
    // Template Dependencies
    //-------------------------------------------------------
    // components: {},
    // directives: {},
    // filters: {},

    mixins: [],
    props: {
        items: {
            type: Array,
            required: false,
        }
    },
}
</script>

<style scoped lang="scss"></style>
