function viewport(){
    var e = window, a = 'inner';
    if ( !( 'innerWidth' in window ) ){
        a = 'client';
        e = document.documentElement || document.body;
    }
    return { 
        w : e[ a+'Width' ] , h : e[ a+'Height' ] 
    }
}

Vue.directive("imagepicker", {
    twoWay: true,

    bind: function () {
        var self = this;
        
        this.handler = function(prop) {
            var v = viewport();
            var width = v.w * .80;
            var height = v.h * .90;
            var left = (screen.width / 2) - (width / 2);
            var top = (screen.height / 2) - (height / 2);
            var win = window.open('/files/image_picker','',"width="+width+",height="+height+",top="+top + ",left="+left);
            var callback = function(url){
                win.close();
                self.set(url);
            }
            win.onload = function() { win.imagePickerCallback = callback; }
            return false;
        }
        this.el.addEventListener("click", this.handler);
    },
    unbind: function () {
        this.el.removeEventListener('click', this.handler)
    }
});
