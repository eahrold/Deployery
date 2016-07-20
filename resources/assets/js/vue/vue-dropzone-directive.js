
Vue.directive("dropzone", {
    twoWay: true,
    params: ['token', 'url', 'options'],

    bind: function () {
        var self = this;

        Dropzone.autoDiscover = false;
        var filedrop = new Dropzone(this.el, {
            url: this.params.url,
            addRemoveLinks: true,

            headers: {
                'Accept': 'application/x.usb.v2+json'
            },
            params: {
                _token: this.params.token
            }
        });

         Dropzone.options.filedrop = {
            paramName: "file",
            maxFilesize: 2,
            accept: function(file, done) {
                console.log(file, done);         
            },
        };

        filedrop.on("success", 
            function (file, response) {
                console.log(response);
                file.id = response.data.id;
                file.resource_uri = response.data.links.resource_uri;
                if(self.vm.models){
                    self.vm.models.unshift(response.data);
                }
                self.set('/'+response.data.path+'/'+response.data.file);
        });

        filedrop.on("removedfile", function(file) {
            self.vm.$http.delete(file.resource_uri).then(
                function(response){
                    var m = self.vm.models;
                    for(var i = 0; i < m.length; i++ ){
                        if (m[i].id == file.id) {
                            return m.splice(i,1);
                        }
                    }
                },
                function(response){
                    console.log('there was a problem removing the file.', response);
                }
            );
        });
    }
});


/// Cant get "clickable" prop to work so duplicate it to act as a button
Vue.directive("dropzone-button", {
    twoWay: true,
    params: ['token', 'url', 'options'],

    bind: function () {
        var self = this;

        Dropzone.autoDiscover = false;
        var filedrop = new Dropzone(this.el, {
            url: this.params.url,
            addRemoveLinks: true,

            headers: {
                'Accept': 'application/x.usb.v2+json'
            },

            params: {
                _token: this.params.token
            },

            previewsContainer: false
        });

         Dropzone.options.filedrop = {
            paramName: "file",
            maxFilesize: 2,
            accept: function(file, done) {
                console.log(file, done);         
            },
        };

        filedrop.on("success", 
            function (file, response) {
                console.log(response);
                file.id = response.data.id;
                file.resource_uri = response.data.links.resource_uri;
                if(self.vm.models){
                    self.vm.models.unshift(response.data);
                }
                self.set('/'+response.data.path+'/'+response.data.file);
        });

        filedrop.on("removedfile", function(file) {
            self.vm.$http.delete(file.resource_uri).then(
                function(response){
                    var m = self.vm.models;
                    for(var i = 0; i < m.length; i++ ){
                        if (m[i].id == file.id) {
                            return m.splice(i,1);
                        }
                    }
                },
                function(response){
                    console.log('there was a problem removing the file.', response);
                }
            );
        });
    }
});

