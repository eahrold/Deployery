<template>
    <div class="form-group" :class='formClass'>
        <label :for='property'>{{ aLabel }}: </label>
        <input type="file" :name="property" :id="property" :multiple="multiple">
        <small>Current File: {{ value }}</small>
        <form-errors :errors='errors' :property='property'></form-errors>
    </div>
</template>

<script>

import { props, errors, watchers } from './FormElementMixins';

export default {
    mixins: [ props, errors, watchers ],

    props : {
        multiple: {
            type: Boolean,
            default: true
        }
    },

    data () {
        return {
            aValue: null
        }
    },

    mounted () {
        this.$nextTick(()=>{
            if(this.value) {
                this.aValue = this.value;
            }
        });
    }
}
</script>