//----------------------------------------------------------
// Creating vue 
// params: 
//      endpoint      = (string) endpoint for the resource
//      el            = (string) document element to bind to the Vue
//      isNew         = Wheter the action is creating a new resource or updating an existing one
//      relationships = json array of arrays of possible includable relationships.
//                      for example if the item can include groups and events
//                      the json array would be 
//                      ```
//                      {
//                          groups : [ {id: 1, title: "group1"}, 
//                                     {id: 2, title: "group2", 
//                                     {id: n, title: "groupN"}
//                                   ],
//                          events : [ {id: 1, title: "event1"}, 
//                                     {id: 2, title: "event2", 
//                                     {id: n, title: "eventN"}
//                                    ]
//                      }
//                      ```
//                      
//      includes      = An array of includes to ask when querying the API to populate the object
//      token         = Session token used for CSFR protection
//                      
//-------------------------------------------------------
function formVue(endpoint, el, isNew, relationships, includes, token, previousURL){

    var primaryForm = new Vue({
        components: {
            'alert': VueStrap.alert,
            'v-select': VueStrap.select,
            'v-option': VueStrap.option
        },

        el: el,
        //----------------------------------------------------------
        // Data
        //-------------------------------------------------------
        data: {
            newRelated: {},
            model: {},
            endpoint: endpoint,

            relationships: relationships,
            includes: includes,

            submitInclude: {},

            showSuccessAlert: false,
            showFailureAlert: false,
            errorBag: [],
            
            saving: false,
            deleting: false,
            pushing: false,

            editSlug: false,
            message: "",
            statusCode: 200,
            isNew: isNew,
            previousURL: previousURL,

            modelLoaded: isNew, // This is used for v-selcetize directive
                                // It won't work correctly if loaded prior
                                // to model data...
        },
       
        //----------------------------------------------------------
        // Computed
        //-------------------------------------------------------
        computed: {
            model_json: function(){
                var json = JSON.parse(JSON.stringify(this.model));
                for (var attrname in this.submitInclude){
                    json[attrname] = this.submitInclude[attrname];
                }
                return json;
            },
            titleAction: function() {
                if(this.isNew) {
                    return "Add ";
                }
                return "Update ";
            },

            queryParams: function(){
                var str = '?';
                var includes = this.includes;
                console.log(includes);
                for(var i=0; i < includes.length; i++){
                    console.log('appending string');
                    if(str.length <= 1)str += "include=";
                    str += includes[i];
                    str += ','
                }
                return str += "&limit=0";
            },
        },

        //----------------------------------------------------------
        // Filters
        //-------------------------------------------------------
        filters: {
            extract: function(value, keyToExtract){
                return value.map(function (item) {
                    return item[keyToExtract];
                });
            },
        },  

        //----------------------------------------------------------
        // Ready
        //-------------------------------------------------------
        ready: function (){
            if(!this.isNew){
                this.get();
            }
        },

        //----------------------------------------------------------
        // HTTP
        //-------------------------------------------------------
        http: {
            headers: {
                'Accept': 'application/x.usb.v2+json',
                'X-XSRF-TOKEN': token,
                'X-Request-Intent': 'cms'
            }
        },

        //----------------------------------------------------------
        // Events
        //-------------------------------------------------------
        events: {
            sortingEnded: function(from, to, type) {
                console.log(type);
                
                this.model[type][from].position = to;
                this.model[type].move(from, to);

                for(var i=0;i < this.model[type].length; i++){
                    this.model[type][i].position = i;
                }
                var json = JSON.stringify(this.model[type]);
                this.sortRelated(type, json);
            }
        },

        //----------------------------------------------------------
        // Methods
        //-------------------------------------------------------
        methods: {
            _successResponse: function(response){
                console.log("Success", response.data);

                this.$set('model', response.data.data);
                this.$set('modelLoaded', true);

                this.$set('isNew', false);
                this.$set('endpoint', response.data.data.links.resource_uri);
                
                this.$set('saving', false);
                this.$set('errorBag', []);
            },

            _errorResponse: function(response){
                console.log("Error", response.data);
                var errors = response.data.errors;
                var message = response.data.message;
                this.$set('saving', false);
                this.$set('errorBag', errors);

                if(errors){
                    message = message+'<ul>';
                    for(var key in  errors){
                        console.log('err', errors[key]);
                        message = message+'<li>'+errors[key]+'<li>';   
                    }
                    message = message+'</ul>'
                }
                alertify.alert(message);

            },

            _presentSuccess: function(message){
                alertify.success(message);
            },

            get: function(){
                var call = this.endpoint+this.queryParams;
                this.$http.get(call).then(
                    function(response){
                        this._successResponse(response);
                        this.$activateValidator();
                    },
                    function(response){
                        this._errorResponse(response);
                    }
                );
            },

            goBack: function(){
                // This is probalby not the best implementation.
               window.location.href = this.previousURL;
            },

            pluck: function(key, keyToExtract){
                if (!(key in this.model)){
                    return [];
                }
                return this.model[key].map(function (item) {
                    return item[keyToExtract];
                });
            },

            insertNew: function(type){
                this.model[type].unshift({})
            },

            toggleSlug: function (){
                this.editSlug = !this.editSlug;
            },

            //----------------------------------------------------------
            // Http Requests
            //-------------------------------------------------------
            push: function(){
                this.pushing = true;
                this.$http.put(this.model.links.resource_uri+'/push').then(
                    function(response){
                        this.pushing = false;
                        this._presentSuccess(response.data.message);
                    },
                    function(response){
                        this.pushing = false;
                        this._errorResponse(response);
                    }
                );
            },
            
            postToSocial: function(){
                var json = JSON.stringify(this.model.social || {});
                this.$http.post(this.model.links.resource_uri+'/social', json).then(
                    function(response){
                        this._presentSuccess(response.data.message);
                    },
                    function(response){
                        this._errorResponse(response);
                    }
                );
            },

            saveRelated: function(item){
                var json = JSON.stringify(item);
                this.$http.put(item.links.resource_uri, json).then(
                    function(response){
                        this._presentSuccess('Successfully saved the related item.');
                    },
                    function(response){
                        this._errorResponse(response);
                    }
                );
            },

            addRelated: function(item, type){
                var json = JSON.stringify(item);
                this.$http.post(this.endpoint+'/' + type, json).then(
                    function(response){
                        this.model[type].unshift(response.data.data);
                        this.newRelated = {};
                        // this._presentSuccess('Successfully added the ' + type);
                    },
                    function(response){
                        this._errorResponse(response);
                    }
                );
            },

            deleteRelated: function(item, type, index){
                var self = this;
                alertify.confirm("Are you sure you want to delte this item?", function(){
                    self.deleting = true;
                    self.$http.delete(item.links.resource_uri).then(
                        function(response){
                        this.model[type].splice(index, 1);
                        //this._presentSuccess('Successfully deleted the ' + type);
                    },
                        function(response){
                            self._errorResponse(response);
                        }
                    );  
                });       
            },

            removeChild: function(id, type, index){
                this.$http.delete(this.endpoint+'/child/' + id).then(
                    function(response){
                        this.model[type].splice(index, 1);
                        // this._presentSuccess('Successfully removed the ' + type);
                    },
                    function(response){
                        this._errorResponse(response);
                    }
                );
            },

            removeRelated: function(id, type, index){
                this.$http.delete(this.endpoint+'/' + type + '/' + id).then(
                    function(response){
                        this.model[type].splice(index, 1);
                        // this._presentSuccess('Successfully removed the ' + type);
                    },
                    function(response){
                        this._errorResponse(response);
                    }
                );
            },

            sortRelated: function(type, jsonArray){
                this.$http.put('/api/'+ type +'/sort', jsonArray).then(
                    function(response){
                        this._presentSuccess(response.data.message);
                    },
                    function(response){
                        this._errorResponse(response);
                });
            },

            delete: function(){
                var self = this;
                alertify.confirm("Are you sure you want to delte this item?", function(){
                    self.deleting = true;
                    self.$http.delete(self.endpoint).then(
                        function(response){
                            return self.goBack();
                        },
                        function(response){
                            self._errorResponse(response);
                        }
                    );  
                });       
            },

            save: function(exit){
                this.saving = true;
                if(this.isNew){
                    this.$http.post(this.endpoint, this.model_json).then(
                        function(response){
                            if(exit === true){
                                return this.goBack();
                            }
                            window.location.href = response.data.data.links.edit_uri;
                        },
                        function(response){
                            this._errorResponse(response);
                        }
                    );
                } 
                else {
                    this.$http.put(this.endpoint, this.model_json).then(
                        function(response){
                            if(exit === true){
                                return this.goBack();
                            }
                            this._successResponse(response);
                        },
                        function(response){
                            this._errorResponse(response);
                        });
                }
            }
        }
    });
}