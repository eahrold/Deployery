const moment = require('moment');

const LocalTime = {
    methods : {
        localTime(time) {
            var time = moment.parseZone(time).local();
            if (time.isValid()) {
                return time.format('LLL');
            }
            return '';
        }
    }
}

export { LocalTime }