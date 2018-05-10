<template>
<div class="card">
      <div v-if='$slots["header"]' class="card-header">
          <slot name='header'></slot>
      </div>
      <div class="card-body">
        <slot></slot>
      </div>
    <div v-if='$slots["footer"]' class="card-footer text-muted">
          <slot name='footer'></slot>
    </div>
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
    props: {
        footerClass: {
            type: String,
            required: false,
        },

        bodyClass: {
            type: String,
            required: false,
        },

        headerClass: {
            type: String,
            required: false,
        },
    },
}
</script>

<style scoped lang="scss"></style>
