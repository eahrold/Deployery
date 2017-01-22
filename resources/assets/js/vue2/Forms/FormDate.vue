<template>
    <div class="form-group" :class='formClass'>
        <label :for='label'>{{ aLabel }}: </label>
        <input :id='id' type="text" :name="aLabel"/>
        <form-errors :errors='errors' :property='property'></form-errors>
    </div>
</template>
<script>

var $ = require('jquery');
var moment = require('moment');
require('bootstrap-daterangepicker');

import { props, errors, dates } from './FormElementMixins';

export default {
    mixins: [ props, errors, dates ],

    data () {
        return {
            single: true,
            id: 'daterange-' + Math.floor(Math.random() * 9999),
            picker: null
        }
    },

    mounted () {
        this.$nextTick(()=>{
            this.load();
        });
    },

    methods : {
        load () {
            var self = this;
            this.picker = $('#'+this.id).daterangepicker(
                this.params, (start, end, label) => {
                self.$emit('input', start.format());
            });
        }
    },

    watch : {
        value (change) {
            this.updateStart(change)
        },
    }
}
</script>

<style type="text/css">
      @import url('../../../../../node_modules/bootstrap-daterangepicker/daterangepicker.css');
</style>