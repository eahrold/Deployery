/**
 *  Slider component
 *
 * Note: to modify the color, override this CSS property
 *    input.switch-toggle-round:checked + label:before {
 *      background-color: #468CC8;
 *   }
 *
 */

<template>
    <div>
        <label class='switch-label pull-left'>{{ aLabel }}</label>
        <div class="switch pull-left">
            <input @change="toggle"
                   v-model='aValue'
                   :id="'switch-' + property"
                   class="switch-toggle switch-toggle-round"
                   type="checkbox">
            <label :for="'switch-' + property"></label>
        </div>
        <div class="clearfix"></div>
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
    },

    methods: {
        toggle(event) {
            this.$emit('toggled', event.target.checked);
        }
    }
}
</script>

<style type="text/css">
.switch-label {
    padding-right: 5px;
}

.switch-toggle {
  position: absolute;
  margin-left: -9999px;
  visibility: hidden;
}

.switch-toggle + label {
  display: block;
  position: relative;
  cursor: pointer;
  outline: none;
  user-select: none;
}

 input.switch-toggle-round + label {
  padding: 2px;
  width: 30px;
  height: 15px;
  background-color: #dddddd;
  border-radius: 15px;
}
input.switch-toggle-round + label:before,
input.switch-toggle-round + label:after {
  display: block;
  position: absolute;
  top: 1px;
  left: 1px;
  bottom: 1px;
  content: "";
}

input.switch-toggle-round + label:before {
  right: 1px;
  background-color: #f1f1f1;
  border-radius: 15px;
  transition: background 0.4s;
}

input.switch-toggle-round + label:after {
  width: 14px;
  background-color: #fff;
  border-radius: 100%;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
  transition: margin 0.4s;
}

input.switch-toggle-round:checked + label:before {
  background-color: #468CC8;
}

input.switch-toggle-round:checked + label:after {
  margin-left: 15px;
}
</style>