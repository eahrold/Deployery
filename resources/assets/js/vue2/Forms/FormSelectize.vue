<template>
    <div class='form-group'>
        <label v-if='label !== ""':for='id'>{{ aLabel }}: </label>
        <select v-if='multiple'
            :id='id'
            :name='property'
            v-model='aValue'
            class="selectpicker form-control"
            :data-live-search="search"
            :data-width="width"
            multiple>

            <!-- Options -->
            <option v-if='nullable' :value="null">{{ placeholder }}</option>
            <option v-for='(opt, idx) in options'
                    :data-content='opt.html'
                    v-bind:value="opt.value || idx">
                {{ opt.text || opt.name || opt }} {{ opt.value}}
            </option>

        </select>

        <select v-else
            :id='id'
            :name='property'
            v-model='aValue'
            class="selectpicker"
            :data-live-search="search"
            :data-width="width">

            <!-- Options -->
            <option v-if='nullable' :value="null">{{ placeholder }}</option>
            <option v-for='(opt, idx) in options'
                    :data-content='opt.html'
                    v-bind:value="opt.value || idx">
                {{ opt.text || opt.name || opt }}
            </option>
        </select>
    </div>
</template>

<script>
import { props, errors } from './FormElementMixins';

require('bootstrap-select');
var $ = require('jquery');

export default {
    mixins: [ props, errors ],

    props: {
        width: {
            type: String,
            default: "auto"
        },

        search: {
            type: Boolean,
            default: true
        },

        options: {
            type: [ Array, Object ],
            required: false
        },

        multiple: {
            type: Boolean,
            default: false
        },

        nullable: {
            type: Boolean,
            default: true
        },

        placeholder: {
            type: String,
            default: "Please Select..."
        }
    },

    data () {
        return {
            aValue: this.multiple ? [] : null,
            id: 'selectize-' + Math.floor(Math.random() * 9999)
        }
    },

    mounted () {
        this.$nextTick(()=>{
            if(this.value) {
                this.aValue = this.value;
            }
            this.refresh();
        });
    },

    methods : {
        refresh () {
            this.$nextTick(()=>{
                $('#'+this.id).selectpicker('refresh');
            });
        }
    },

    watch : {
        options (change) {
            this.refresh();
        },

        aValue (change) {
            this.$emit('input', change);
        },

        value (change) {
            this.aValue = change;
            this.refresh();
        }
    }
}
</script>

<style type="text/css">
    @import url('../../../../../node_modules/bootstrap-select/dist/css/bootstrap-select.min.css');
</style>