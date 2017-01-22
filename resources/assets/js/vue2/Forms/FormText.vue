<template>
    <div class="form-group" :class='formClass'>
        <label :for='property'>{{ aLabel }}: </label>
        <input type="text" :id="property" v-model='aValue' class="form-control" :placeholder="placeholder">
        <form-errors :errors='errors' :property='property'></form-errors>
    </div>
</template>

<script>

import { props, errors, watchers } from './FormElementMixins';

export default {
    mixins: [ props, errors, watchers ],

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