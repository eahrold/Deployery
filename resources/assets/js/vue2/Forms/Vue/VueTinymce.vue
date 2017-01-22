<template>
    <div>
        <textarea v-if="isReady" :id="element">
        </textarea>
    </div>
</template>

<script>

    var elFinderBrowser = function (field_name, url, type, win) {
      tinymce.activeEditor.windowManager.open({
        file: '/elfinder/tinymce4',// use an absolute path!
        title: 'Tinymce File Manager',
        width: 900,
        height: 450,
        resizable: 'yes'
      }, {
        setUrl: function (url) {
          win.document.getElementById(field_name).value = url;
        }
      });
      return false;
    }

    export default {
        props: {
            value: {
                required: true
            },

            property: {
                type: String,
            },

            content: {
                type: String,
            },

            headers: {
                type: Object,
                default: () => { return {}; }
            },

            isReady: {
                type: Boolean,
                default: true
            },

            css: {
                type: String,
                default: ""
            },

            height: {
                type: Number,
                default: 300
            },

            menubar: {
                type: Boolean,
                default: false
            },

            plugins: {
                type: Array,
                default: () => { return [
                    'advlist autolink lists link image charmap print preview anchor',
                    'searchreplace visualblocks code fullscreen',
                    'insertdatetime media table contextmenu paste code'
                ]; }
            },

            toolbar: {
                type: String,
                default: 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image'
            }
        },

        data () {
            return {
                selector: null,
                tinymce: null
            }
        },

        computed : {
            element () {
                return 'tinymce-' + Math.floor(Math.random() * 9999);
            }
        },

        mounted () {
            if(this.isReady) {
                this.$nextTick(()=>{
                    this.loadTinymce();
                });
            }
        },

        destroyed () {
            tinymce.remove("#"+this.element);
        },

        watch: {
            // whenever question changes, this function will run
            isReady: function (ready) {
                if(ready && !this.tinymce) {
                    this.loadTinymce()
                }
            }
        },

        methods: {
            loadTinymce () {
                var self = this;
                var selector = "#"+this.element;
                this.tinymce = tinymce.init({
                    selector: selector,
                    height: self.height,
                    menubar: false,
                    plugins: self.plugins,
                    toolbar: self.toolbar,
                    content_css: self.css,
                    file_browser_callback : elFinderBrowser,
                    image_caption: true,

                    setup: (editor) => {
                        // init tinymce
                        editor.on('init', function() {
                            editor.setContent(self.value || "");
                        });

                        editor.on('change', function() {
                            self.$emit('input', editor.getContent());
                        });
                    }
                });
            }
        }
    }
</script>


