<template>
    <div class="form-group">
        <label :for='property'>{{ aLabel }}: </label>
        <br>

        <div class='form-group-radio'>
            <template v-for='(opt, idx) in options'>
                <input type="radio"
                    :name="property"
                    :id="property+'-'+idx"
                    v-bind:value="opt.value || idx"
                    v-model='aValue'>
                <label :for="property+'-'+idx">
                    {{ opt.text || opt.name || opt }}
                </label>
                <br>
            </template>
        </div>
    </div>
</template>

<script>

import { props, errors, watchers, options } from './FormElementMixins';

export default {
    mixins: [ props, errors, watchers, options ],

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
    },
}
</script>