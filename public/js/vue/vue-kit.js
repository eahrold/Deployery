Vue.filter('betweenDates', function(array, key, start, end) {
    if( !start && !end ){ return array; }
    var start = moment(start);
    var end = moment(end);
    return array.filter(function(item){
        var d = moment(item[key]);
        if(d > start){ return (end && d > end) == false; }
        return false;
    });
})

Vue.filter('truncate', function(string, length) {
    length = length || 25;
    if(string.length < length){ return string; }
    return string.substring(0, length)+"..."
});

Vue.filter('sluggify', function(string) {
    if(!string) { return ""; }
    return string.toLowerCase()
        .replace(/\s+/g, '-')
        .replace(/[^\w\-]+/g, '')
        .replace(/\-\-+/g, '-')
        .replace(/^-+/, '')
        .replace(/-+$/, '')
        .substring(0,100);
});

Vue.filter('localizedDate', function(dateString){
    if(!dateString){ return; }
    return moment(dateString).format('ll');
});

Vue.filter('localizedDatetime', function(dateString){
    if(!dateString){ return; }
    return moment(dateString).format('llll');
});


//# sourceMappingURL=vue-kit.js.map
