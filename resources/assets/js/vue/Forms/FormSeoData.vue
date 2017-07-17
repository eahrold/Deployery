<template>
    <div class="panel-group">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h4 class="panel-title">
            <a data-toggle="collapse" href="#seo-panel">SEO Data</a>
          </h4>
        </div>
        <div id="seo-panel" class="panel-collapse collapse in">
          <div class="panel-body">
            <h4>Meta Tags</h4>
            <div class="form-group" :class='formClass'>
                <label for='meta_title'>Meta Title: </label>
                <input type="text" id="meta_title" v-model='aValue.meta_title' class="form-control">
                <form-errors :errors='errors' property='meta_title'></form-errors>
            </div>

            <div class="form-group" :class='formClass'>
                <label for='meta_description'>Meta Description: </label>
                <input type="text" id="meta_description" v-model='aValue.meta_description' class="form-control">
                <form-errors :errors='errors' property='meta_description'></form-errors>
            </div>

            <div class="form-group" :class='formClass'>
                <label for='meta_keywords'>Meta Keywords: </label>
                <input type="text" id="meta_keywords" v-model='aValue.meta_keywords' class="form-control">
                <form-errors :errors='errors' property='meta_keywords'></form-errors>
            </div>

            <h4>OG Tags</h4>
            <div class="form-group" :class='formClass'>
                <label for='og_title'>OG Title: </label>
                <input type="text" id="og_title" v-model='aValue.og_title' class="form-control">
                <form-errors :errors='errors' property='og_title'></form-errors>
            </div>

            <div class="form-group" :class='formClass'>
                <label for='og_description'>OG Description: </label>
                <input type="text" id="og_description" v-model='aValue.og_description' class="form-control">
                <form-errors :errors='errors' property='og_description'></form-errors>
            </div>

            <form-elfinder v-model="aValue.og_image" label="OG Image" property='og_image'></form-elfinder>

          </div>
        </div>
      </div>
    </div>
</template>

<script>

import { props, errors } from './FormElementMixins';

export default {

    mixins: [ props, errors ],

    data () {
        return {
            aValue: {
                meta_title: null,
                meta_description: null,
                meta_keywords: null,
                og_title: null,
                og_description: null
            }
        }
    },

    mounted () {
        this.$nextTick(()=>{
            if(this.value) {
                this.aValue = this.value;
            }
        });
    },

    watch : {
        aValue: {
            handler: function(change){
                this.$emit('input', change);
            },
            deep: true
        },
    }
}
</script>