alertify.defaults = {
    // dialogs defaults
    modal:true,
    basic:false,
    frameless:false,
    movable:true,
    moveBounded:false,
    resizable:true,
    closable:true,
    closableByDimmer:true,
    maximizable:true,
    startMaximized:false,
    pinnable:true,
    pinned:true,
    padding: true,
    overflow:true,
    maintainFocus:true,
    transition:'fade',
    autoReset:true,

    // notifier defaults
    notifier:{
        // auto-dismiss wait time (in seconds)  
        delay:3,
        // default position
        position:'bottom-right'
    },

    // language resources 
    glossary:{
        // dialogs default title
        title:'USB API',
        // ok button text
        ok: 'OK',
        // cancel button text
        cancel: 'Cancel'            
    },

    // theme settings
    theme:{
        // class name attached to prompt dialog input textbox.
        input:'ajs-input',
        // class name attached to ok button
        ok:'ajs-ok',
        // class name attached to cancel button 
        cancel:'ajs-cancel'
    }
};


Vue.filter('betweenDates', function(array, key, start, end) {
    if( !start && !end ){
        return array;
    }

    var start = moment(start);
    var end = moment(end);

    return array.filter(function(item){
        var d = moment(item[key]);
        if(d > start){
            if(end && d > end){
                return false;
            }
            return true;
        }
        return false;
    });
})

Vue.filter('sluggify', function(string) {
    if(!string) {
        return "";
    }
    return string.toLowerCase()
        .replace(/\s+/g, '-')
        .replace(/[^\w\-]+/g, '')
        .replace(/\-\-+/g, '-')
        .replace(/^-+/, '')
        .replace(/-+$/, '')
        .substring(0,100);
});

Vue.filter('localizedDate', function(dateString){
    if(!dateString){
        return;
    }
    return moment(dateString).format('ll');
});

Vue.filter('localizedDatetime', function(dateString){
    if(!dateString){
        return;
    }
    return moment(dateString).format('llll');
});

Vue.transition('expand', {

  beforeEnter: function (el) {
    el.textContent = 'beforeEnter'
  },
  enter: function (el) {
    el.textContent = 'enter'
  },
  afterEnter: function (el) {
    el.textContent = 'afterEnter'
  },
  enterCancelled: function (el) {
    // handle cancellation
  },

  beforeLeave: function (el) {
    el.textContent = 'beforeLeave'
  },
  leave: function (el) {
    el.textContent = 'leave'
  },
  afterLeave: function (el) {
    el.textContent = 'afterLeave'
  },
  leaveCancelled: function (el) {
    // handle cancellation
  }
})
