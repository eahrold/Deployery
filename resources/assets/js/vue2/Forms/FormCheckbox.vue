<template>
    <div class="form-group" :class='formClass'>
        <input v-model='aValue'
               :id="'checkbox-' + property"
               type="checkbox">
        <label :for="'checkbox-' + property"> {{ aLabel }}</label>
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