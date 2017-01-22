<template>
    <div class="form-group" :class='formClass'>
        <label :for='property'>{{ aLabel }}: </label>
        <div class='input-group'>
            <input type="text" :id="element" :name='property' v-model='aValue' class="form-control">
            <span class="input-group-btn">
                <button class="popup_selector btn btn-secondary" type="button" @click='openElFinder'>
                    <i class="fa fa-folder-open" aria-hidden="true"></i>
                </button>
            </span>
        </div>
        <form-errors :errors='errors' :property='property'></form-errors>
    </div>
</template>

<script>

import { props, errors, watchers } from './FormElementMixins';

require('jquery-colorbox');

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
            this.setupElfinder();
        });
    },

    computed : {
        element () {
            return 'elfinder-' + Math.floor(Math.random() * 9999);
        }
    },

    methods : {
        openElFinder (event) {
            event.preventDefault();

            var elfinderUrl = '/elfinder/popup/';
            var triggerUrl = elfinderUrl + this.element;

            $.colorbox({
                href: triggerUrl,
                fastIframe: true,
                iframe: true,
                width: '90%',
                height: '500px'
            });
        },

        setupElfinder() {
            var self = this;
            window.document.addEventListener('fileSelected', (event)=>{
                console.log(event.detail);
                if(event.detail.el === this.element) {
                    self.aValue = "/" + event.detail.file;
                }
            }, false);
        }
    }
}
</script>