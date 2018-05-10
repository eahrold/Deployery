<template>
<ul class="list-group">
    <li v-if='items' v-for='(item, idx) in items' :key='idx' class="list-group-item">
        <slot v-bind:item="item">
            <!-- Fallback content -->
            {{ todo.text }}
        </slot>
    </li>
</ul>
</template>

<script type="text/javascript">

export default {
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
