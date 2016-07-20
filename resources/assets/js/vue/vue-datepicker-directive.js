Vue.directive('datepicker', {
  bind: function () {
    var vm = this.vm;
    var key = this.expression;
    var picker = $(this.el).datetimepicker({
      format: "YYYY-MM-DD"
    });

    picker.change(function (date) {
      console.log("date", date)
      vm.$set(key, date);
    });
  },
  update: function (val) {
    $(this.el).datetimepicker('setDate', val);
  }
});


Vue.directive('datetimepicker', {
  bind: function () {
    var vm = this.vm;
    var key = this.expression;
    var picker = $(this.el).datetimepicker({
      format: "YYYY-MM-DD HH:mm:ss"
    });

    picker.change(function (date) {
      console.log("changed", date);
      vm.$set(key, date);
    });
  },
  update: function (val) {
    console.log("update", val);
    $(this.el).datetimepicker('setDate', val);
  }
});

