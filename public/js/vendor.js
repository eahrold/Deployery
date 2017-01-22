var Alerter = {
    settings: {
        layout: 'center',
        theme: 'relax',
        killer: true,
        timeout: 5000,
        closeWith: ['click', 'backdrop'],
        animation: {
            open: 'animated flipInX', // Requires Animate.css
            close: 'animated flipOutX', // Requires Animate.css
        }
    },
    error: function(message){
        var _settings = this.settings;
        _settings.text = message;
        _settings.type = 'error';
        noty(_settings);
        return this;
    },
    alert: function(message){
        var _settings = this.settings;
        _settings.text = message;
        _settings.type = 'alert';
        noty(_settings);
        return this;
    },
    success: function(message){
        var _settings = this.settings;
        _settings.text = message;
        _settings.timeout = 3000;
        _settings.type = 'success';
        noty(_settings);
        return this;
    },
    top: function(){
        this.settings.layout = 'topRight';
        return this;
    },
    bottom: function(){
        this.settings.layout = 'bottomRight';
        return this;
    },
    center: function(){
        this.settings.layout = 'center';
        return this;
    },
};
//# sourceMappingURL=vendor.js.map
