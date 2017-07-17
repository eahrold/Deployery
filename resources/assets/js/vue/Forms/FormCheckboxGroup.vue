<template>
    <div class="form-group">
        <label :for='property'>{{ aLabel }}: </label>
        <br>

        <div class='form-group-checkbox'>
            <template v-for='(opt, idx) in options'>
                <input type="checkbox"
                    :name="'checkbox-'+property+'-'+idx"
                    :id="'checkbox-'+property+'-'+idx"
                    v-bind:value="opt.value || idx"
                    v-model='aValue'>
                <label :for="'checkbox-'+property+'-'+idx">
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
    }
}
</script>