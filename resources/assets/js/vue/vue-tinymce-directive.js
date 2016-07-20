;(function () {

  var imageFilePicker = function (callback, value, meta) {               
      tinymce.activeEditor.windowManager.open({
          title: 'Choose Image',
          url: '/files/image_picker',
          width: 650,
          height: 550,
          buttons: [{
              text: 'Close',
              onclick: 'close'
          }],
          },{
          oninsert: function (url) {
              callback(url);
          },
      });
  };

  var vTinyMCE = {}
  // exposed global options
  vTinyMCE.config = {}

  vTinyMCE.install = function (Vue) {
    Vue.directive('tinymce-editor',{
        twoWay: true,
        deep: true,
        params: [
          'text',
          '_defer'
        ],

        paramWatchers: {
          text: function (val, oldVal) {
            var editor = tinymce.get(this.el.id);
            var initialized = this.params._defer;
            if(val && editor && !initialized){
               editor.setContent(val);
               this.params._defer = true;
            }
          }
        },

        bind: function() {
          var self = this;

          tinymce.init({
            selector: '#'+self.el.id,
            //----------------------------------------------------------
            // INCLUDE THE PLUGIN
            //-------------------------------------------------------

            theme: 'modern',
            height: 500,

            plugins: [
              "advlist autolink lists link image charmap print preview anchor",
              "searchreplace visualblocks codemirror fullscreen textcolor contextmenu",
              "insertdatetime media table contextmenu paste imagetools"
            ],

            contextmenu: "link image | bold italic | inserttable cell row column deletetable | alignleft aligncenter alignright alignjustify",
            contextmenu_never_use_native: true,

            cssFiles: [
               'theme/neat.css',
               'theme/elegant.css'
            ],
            codemirror: {
              indentOnInit: false, // Whether or not to indent code on init.
              path: 'CodeMirror', // Path to CodeMirror distribution
              config: {           // CodeMirror config object
                 mode: 'application/x-httpd-php',
                 lineNumbers: true
              },  
              jsFiles: [          // Additional JS files to load
                    'mode/clike/clike.js',
                    'mode/php/php.js',
                    'addon/hint/html-hint.js',
                    'addon/edit/matchtags.js',
                    'lib/util/formatting.js' // !!! This is NOT included in the default CodeMirror distro and is not supported. Grab it from the Codemirror v2.38.
              ],

              loaded: function(codemirror){
                  // AutoFormat on load
                  var totalLines = codemirror.lineCount();
                  var totalChars = codemirror.getValue().length;
                  codemirror.autoFormatRange({line:0, ch:0}, {line:totalLines, ch:totalChars});
                  codemirror.doc.setCursor(0,0);
              }
            },

            //----------------------------------------------------------
            // PUT PLUGIN'S BUTTON on the toolbar
            //-------------------------------------------------------

            toolbar1: "alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | code",
            toolbar2: "styleselect | bold italic | fontselect fontsizeselect forecolor",

            //----------------------------------------------------------
            // Image Settings
            //-------------------------------------------------------
            // imagetools_cors_hosts: ['*'],
            image_caption: true,
            image_advtab: true,

            //----------------------------------------------------------
            // SET RELATIVE_URLS to FALSE
            // (This is required for images to display properly)
            //-------------------------------------------------------

            relative_urls : false,
            remove_script_host : false,
            convert_urls : true,

            file_picker_callback: imageFilePicker,

            //----------------------------------------------------------
            // CSS for content body
            //-------------------------------------------------------
            content_css: '/css/usb.org.css',

            //----------------------------------------------------------
            // Setup
            //-------------------------------------------------------
            setup: function(editor) {
              // init tinymce

              editor.on('init', function() {
                  editor.setContent(self.params.text || self.value || '');
              });
              
              editor.on('change', function() {
                  self.set(editor.getContent());
              });
            }
          });
        },
        
        update: function(newVal, oldVal) {
           
        }
      });
  }

  if (typeof exports == "object") {
    module.exports = vTinyMCE
  } else if (typeof define == "function" && define.amd) {
    define([], function () {
      return vTinyMCE
    })
  } else if (window.Vue) {
    window.vTinyMCE = vTinyMCE
    Vue.use(vTinyMCE)
  }
})();
