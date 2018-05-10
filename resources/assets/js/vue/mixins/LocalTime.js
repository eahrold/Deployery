const moment = require('moment');

const LocalTime = {
    methods : {
        localTime(time) {
            var time = moment.parseZone(time).local();
            if (time.isValid()) {
                return time.format('LLL');
            }
            return '';
        },

        timeFromNow(time) {
            return moment(time).fromNow();
        }
    }
}

export { LocalTime }