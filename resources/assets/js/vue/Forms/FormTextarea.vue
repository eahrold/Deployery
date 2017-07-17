<template>
    <div class="form-group" :class='formClass'>
        <label :for='property'>{{ aLabel }}: </label>
        <textarea :id="property" :rows='rows' v-model='aValue' class="form-control"></textarea>
        <form-errors :errors='errors' :property='property'></form-errors>
    </div>
</template>

<script>

import { props, errors, watchers } from './FormElementMixins';

export default {
    mixins: [ props, errors, watchers ],

    props: {
        rows: {
            required: false,
            default: 3
        },
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
