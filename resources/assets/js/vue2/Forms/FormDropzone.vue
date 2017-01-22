<template>
    <div class="form-group upload">
        <label>{{ label }} :</label>
        <dropzonejs :existing='value'
                    :url="endpoint"
                    :accepted-file-types="fileTypes"
                    :headers='requestHeaders'
                    :max-number-of-files='limit'
                    :max-file-size-in-mb='maxFileSize'
                    v-on:vdropzone-success="dzAdded"
                    v-on:vdropzone-removed-file="dzRemoved">
        </dropzonejs>
    </div>
</template>

<script>
    _ = require('lodash');

    export default {
        props: {
            // Used for v-bind
            value: {
                type: [ Object, Array ],
            },

            // the label for the form element
            label: {
                type: String,
                required: true
            },

            // The type of model
            type: {
                type: String,
                required: true
            },

            // The id of the model
            id: {
                type: Number,
            },

            // The model property the image should be attached to
            property: {
                type: String,
            },

            // Supported file types
            fileTypes: {
                type: String,
                default: 'image/*'
            },

            // If multiple files are allowed, how many
            limit: {
                type: Number,
                default: 1
            },

            // If multiple files are allowed, how many
            maxFileSize: {
                type: Number,
                default: 2
            },

            // Error callback
            error: {
                type: Function
            }
        },

        data () {
            return {
                base: "/api/upload"
            }
        },

        computed: {
            endpoint() {
                var endpoint =  this.base + "?type=" +this.type;

                if (this.id) {
                    endpoint += "&model=" + this.id;
                }

                if (this.property) {
                    endpoint += "&property=" + this.property;
                }

                return endpoint;
            },

            fileList () {
                if (this.value instanceof Array) {
                    return this.value;
                }
                else if (this.value instanceof Object) {
                    return [ this.value ];
                }
            },

            requestHeaders () {
                return {
                    'X-CSRF-TOKEN': Laravel.csrfToken,
                    'Authorization': 'Bearer ' + Laravel.apiToken
                };
            },

            impliedArray() {
                return Boolean(this.limit > 1 || this.value instanceof Array);
            }
        },

        methods: {
            _error (response) {
                if(this.error) {
                    this.error(response);
                }
            },

            dzError (response) {
                this._error(response);
            },

            dzAdded (file) {
                if(this.impliedArray) {
                    var files = this.value || [];
                    files.push(file);
                    return this.$emit('input', files);
                }
                this.$emit('input', file);
            },

            dzRemoved (file) {
                var success = (response) => {
                    if(this.impliedArray) {
                        var files = _.filter(this.value, (f)=>{
                            return f.id != file.id
                        });
                        return this.$emit('input', files);
                    }
                    this.$emit('input', null);
                };

                var error = (response) => {
                    this._error(response)
                    console.log('FormDropzone Error:', response);
                };

                var endpoint = this.base + "/" + file.id;
                this.$http.delete(endpoint).then(success, error);
            },
        }
    }
</script>
