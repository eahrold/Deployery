;(function () {

  var vSortable = {}
  var Sortable = typeof require === 'function'
      ? require('sortablejs')
      : window.Sortable

  if (!Sortable) {
    throw new Error('[vue-sortable] cannot locate Sortable.js.')
  }

  // exposed global options
  vSortable.config = {}

  vSortable.install = function (Vue) {
    Vue.directive('sortable', function (options) {
        var self = this;
        var type = options ? options['type'] : null;
        var parent = options ? options['parent'] : null;
        var settings = {
            onEnd: function(evt){
                self.vm.$emit('sortingEnded', evt.oldIndex, evt.newIndex, type, parent);
            }
        }
        options = jQuery.extend(settings, options);
        this.sortable = new Sortable(this.el, options)
    });
  }

  if (typeof exports == "object") {
    module.exports = vSortable
  } else if (typeof define == "function" && define.amd) {
    define([], function () {
      return vSortable
    })
  } else if (window.Vue) {
    window.vSortable = vSortable
    Vue.use(vSortable)
  }

})();