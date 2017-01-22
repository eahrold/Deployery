<template>

<form :action="url" class="dropzone" :id="id"></form>

</template>

<script>
var Dropzone = require('dropzone')
Dropzone.autoDiscover = false

export default {
    props: {
        value: {
            type: String
        },
        url: {
            type: String,
            required: true
        },
        clickable: {
            type: Boolean,
            default: true
        },
        acceptedFileTypes: {
            type: String
        },
        thumbnailHeight: {
            type: Number,
            default: 150
        },
        thumbnailWidth: {
            type: Number,
            default: 150
        },
        showRemoveLink: {
            type: Boolean,
            default: true
        },
        maxFileSizeInMB: {
            type: Number,
            default: 2
        },
        maxNumberOfFiles: {
            type: Number,
            default: 5
        },
        autoProcessQueue: {
            type: Boolean,
            default: true
        },
        useCustomDropzoneOptions: {
            type: Boolean,
            default: false
        },
        headers: {
            type: Object,
            default: {}
        },
        dropzoneOptions: {
            type: Object
        },
        existing: {
            type: [Object, Array],
        }
    },

    mounted () {
      this.$nextTick(()=>{
        this.loadDropzone();
        this.preload();
      });
    },

    data () {
        return {
            id: 'dropzone-' + Math.floor(Math.random() * 999)
        }
    },

    computed: { },

    methods: {
        loadDropzone () {
            var element = document.getElementById(this.id)

            if(this.dropzone) {
                this.dropzone.destroy();
            }

            if (!this.useCustomDropzoneOptions) {
                this.dropzone = new Dropzone(element, {
                    url: this.url,
                    clickable: this.clickable,
                    headers: this.headers,
                    thumbnailWidth: this.thumbnailWidth,
                    thumbnailHeight: this.thumbnailHeight,
                    maxFiles: this.maxNumberOfFiles,
                    maxFilesize: this.maxFileSizeInMB,
                    dictRemoveFile: 'Remove',
                    addRemoveLinks: this.showRemoveLink,
                    acceptedFiles: this.acceptedFileTypes,
                    autoProcessQueue: this.autoProcessQueue,
                    dictDefaultMessage: '<i class="fa fa-2x fa-cloud-upload" aria-hidden="true"></i><br>Drop files here to upload',
                })
            } else {
                this.dropzone = new Dropzone(element, this.dropzoneOptions)
            }

            // Handle the dropzone events
            var self = this
            this.dropzone.on('addedfile', function (file) {
                self.$emit('vdropzone-fileAdded', file);
            })

            this.dropzone.on('removedfile', function (file) {
                self.$emit('vdropzone-removedFile', file)
            })

            this.dropzone.on('success', function (file, response) {
                file.id = response.id;
                self.$emit('vdropzone-success', response)
            })

            this.dropzone.on('error', function (file, error, xhr) {
                self.$emit('vdropzone-error', file, error, xhr)
            })
        },

        preload () {
            if (!this.existing || !this.dropzone) {
                return;
            }

            var add = (file)=>{
                var mockFile = {
                    name: file.filename,
                    size: file.filesize ,
                    id: file.id,
                    status: 'success',
                    processing: false
                };
                this.dropzone.emit("addedfile", mockFile);
                this.dropzone.emit("thumbnail", mockFile, file.thumbnail_sm || file.thumbnail);
                mockFile.previewElement.classList.add('dz-complete');
            };

            if (this.existing instanceof Array) {
                for (var i = this.existing.length - 1; i >= 0; i--) {
                  add(this.existing[i])
                }
            } else {
                add(this.existing);
            }
        }
    },

    events: {
        removeAllFiles: function () {
            this.dropzone.removeAllFiles(true)
        },
        processQueue: function () {
            this.dropzone.processQueue()
        }
    },
}

</script>

<style>
    @import url('../../../../../../node_modules/dropzone/dist/dropzone.css');
    @import url('../../../../../../node_modules/dropzone/dist/basic.css');

    form.dropzone {
        min-height: 200px;
    }

    .dropzone .dz-preview .dz-image img {
        position: absolute;
        right: 0;
        top: 0;
        bottom: 0;
        left: 0;
        margin:auto;
    }

</style>
