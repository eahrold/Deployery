/**
 *  Date Range Picker Component
 *
 *  Usage:
 *   <form-daterange :start='start_prop'
 *                   @start='start_prop = arguments[0]'
 *                   :end='end_prop'
 *                   @end='end_prop = arguments[0]'
 *                   label='Some Label'>
 *   </form-daterange>
 *
 */


<template>
    <div class="form-group" :class='formClass'>
        <label :for='label'>{{ label }}: </label>
        <input :id='id' type="text" :name="label"/>
        <form-errors :errors='errors' :property='label'></form-errors>
    </div>
</template>
<script>

var $ = require('jquery');
var moment = require('moment');
require('bootstrap-daterangepicker');

import { errors, dates } from './FormElementMixins';

export default {
    mixins: [ errors, dates ],

    props: {
        property: {
            type: String,
            required: true,
        },

        errors: {
            type: Object,
            required: false
        },

        start : {
            type: String,
        },

        end : {
            type: String,
        },
    },

    data () {
        return {
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
            this.picker = $('#'+this.id).daterangepicker({
                "autoApply": true
            }, (start, end, label) => {
                self.$emit('start', start.format());
                self.$emit('end', end.format());
            });
        }
    },

    watch : {
        start (change) {
            this.updateStart(change)
        },

        end (change) {
            this.updateEnd(change)
        }
    }
}
</script>

<style type="text/css">
      @import url('../../../../../node_modules/bootstrap-daterangepicker/daterangepicker.css');
</style>