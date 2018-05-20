const moment = require('moment');

const timeFunctions = {
    localTime: function(time) {
        if( ! time)return ""

        var time = moment.parseZone(time).local();
        if (time.isValid()) {
            return time.format('LLL');
        }
        return '';
    },
    timeFromNow: function(time) {
        return moment(time).fromNow()
    }
}

const LocalTime = {
    filters: timeFunctions,
    methods : timeFunctions
}

export { LocalTime }