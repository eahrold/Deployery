var props = {
    props: {
        value : {
            required: true
        },

        placeholder : {
            type: [String, Number],
            default: ""
        },

        property : {
            type: String,
            required: true,
        },

        label: {
            type: String,
            required: false
        },

        errors: {
            type: Object,
            required: false
        }
    },

    computed : {
        aLabel () {
            var string = (this.label || this.property || '').replace(/_/g, " ");
            return string.charAt(0).toUpperCase() + string.slice(1);
        }
    }
};

var options = {
    props: {
        options: {
            type: [Array, Object],
            required: false
        }
    },
};

var watchers = {
    watch : {
        aValue (change) {
            this.$emit('input', change);
        },

        value (change) {
            this.aValue = change;
        }
    }
};

var errors = {
    computed : {
        formClass() {
            return [
                { "has-error": this.hasError }
            ];
        },

        typeErrors() {
            return this.errors[this.property];
        },

        hasError () {
            return Boolean(this.errors && this.typeErrors);
        },
    }
};

var dates = {
    props: {
        timepicker: {
            type: Boolean,
            default: false
        },

        dropdowns: {
            type: Boolean,
            default: false
        },

        autoApply: {
            type: Boolean,
            default: true
        },

        opens: {
            type: String,
            default: "center"
        }
    },

    methods: {
        safeDate (string) {
            var date = moment(string);
            if (date.isValid()) {
                return date;
            }
            return moment({});
        },

        updateStart (string) {
            var date = moment(string);
            if (date.isValid()) {
                this.picker.data('daterangepicker').setStartDate(date);
            }
        },

        updateEnd (string) {
            var date = moment(string);
            if (date.isValid()) {
                this.picker.data('daterangepicker').setEndDate(date);
            }
        }
    },

    computed : {
        params () {
            var date = this.safeDate(this.value);
            return {
                "singleDatePicker": this.single || false,
                "autoApply": this.autoApply,
                "showDropdowns": this.dropdowns,
                "timePicker": this.timepicker,
                "startDate": date,
                "endDate": date,
                "opens": this.opens
            }
        }
    }
}

export { props, errors, watchers, dates, options }
