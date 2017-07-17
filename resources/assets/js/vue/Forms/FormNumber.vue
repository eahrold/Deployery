<template>
    <div class="form-group" :class='formClass'>
        <label :for='property'>{{ aLabel }}: </label>
        <input type="number" :name='property' :min='min' :max='max' :id="property" v-model.number='aValue' :placeholder="placeholder" class="form-control">
        <form-errors :errors='errors' :property='property'></form-errors>
    </div>
</template>

<script>

import { props, errors, watchers } from './FormElementMixins';

export default {
    mixins: [ props, errors, watchers ],

    props: {
        min: {
            type: [ Number, String ],
            default: Number.MIN_VALUE
        },

        max: {
            type: [ Number, String ],
            default: Number.MAX_VALUE
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