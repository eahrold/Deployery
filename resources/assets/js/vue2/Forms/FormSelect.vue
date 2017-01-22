<template>
    <div class="form-group" :class='formClass'>
        <label for='theme'>{{ aLabel }}</label>
        <select v-model='aValue' :name='property'>
            <option v-for='(opt, idx) in options' v-bind:value="opt.value || idx">{{ opt.text || opt.name || opt }}</option>
        </select>
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

        options: {
            type: [ Array, Object ],
            required: false
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