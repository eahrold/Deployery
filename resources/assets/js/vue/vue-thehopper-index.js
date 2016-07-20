function indexVue(endpoint, params, el){
    const MAX_PAGE = params.totalModels;
    const API_REQUEST_PAGE_LOAD = 100;

    var indexVue = new Vue({
        components: {
            'alert': VueStrap.alert,
        },

        el: el || '#vm_index_table',
        //----------------------------------------------------------
        // Data
        //-------------------------------------------------------
        data: {
            arr: [],

            models: [],
            relationships: params.relationships,
            includes: params.includes,
            
            pagination: {},
            errorBag: [],
            loading: false,
            message: "",
            endpoint: endpoint,
            
            // Filters
            showFilter: false,

            states: params.states,
            selectedState: 0,

            tags: params.tags,
            selectedTag: null,

            // Alert
            showSuccessAlert: false,
            showFailureAlert: false,

            // Pagination properites
            search: "",
            from_date: null,
            to_date: null,

            initURL: window.location.href,

            page: 1,
            pageSize: 20,
            paginationOptions: [
              { text: '20', value: 20 },
              { text: '50', value: 50 },
              { text: '100', value: 100 },
              { text: 'All', value: params.totalModels }
            ],
            displayedItemsLength: 0,
            totalModels: params.totalModels,
        },
        
        //----------------------------------------------------------
        // Computed
        //-------------------------------------------------------
        computed: {
            percentLoaded: function(){
                return Math.round(( this.displayedItemsLength / this.totalModels) * 100);
            },

            pageCount: function(){
                return Math.floor(this.displayedItemsLength/this.pageSize)+1;
            },

            hasMultiplePages: function(){
                return this.pageCount > 1;
            },

            models_json: function(){
                return JSON.parse(JSON.stringify(this.models));
            },

            stateModels: function(){
                var id = this.selectedState;
                if(!id == null || id == '' || id == 0 ){
                    return this.models
                }
                return this.models.filter(function(item){
                    return item.state_id == id;  
                });
            },

            queryParams: function(){
                var str = '?';
                var includes = this.includes;

                for(var i=0;i<includes.length; i++){
                    if(str.length <= 1)str += "include=";
                    str += includes[i];
                    str += ','
                }

                var p = this.pagination;
                if(p.current_page < p.total_pages){
                    str += '&page='+(p.current_page+1);
                }
                str += '&limit='+API_REQUEST_PAGE_LOAD;
                return str;
            }
        },

        //----------------------------------------------------------
        // Filters
        //-------------------------------------------------------
        filters: {
            paginate: function(array) {
                if(!array)return;
                this.$set('displayedItemsLength',  array.length);

                var offset = (this.page-1)*this.pageSize;
                var end = offset+Math.min(this.pageSize, MAX_PAGE);
                var val = array.slice(offset, end);
                return val;
            },

            byTags: function(array){
                // Clear the docket with some early returns
                if(!array || !array.length || !array[0].tags){
                    return array;
                }

                var selectedTag = this.selectedTag;
                if(!selectedTag || selectedTag == 0){
                    return array;
                }

                return array.filter(function (item){
                    for(var i=0; i < item.tags.length; i++){
                        if(item.tags[i].id == selectedTag){
                            return true;
                        }
                    }
                    return false;
                });
            },

            count: function(array){
                var length = array.length;
                this.displayedItemsLength = length;
                return length;
            },

            extract: function(value, keyToExtract){
               return value.map(function (item) {
                    return item[keyToExtract];
                });
            }

        },

        //----------------------------------------------------------
        // Ready
        //-------------------------------------------------------
        ready: function (){
            this.loading = true;
            this.getModels(this.endpoint);
        },

        //----------------------------------------------------------
        // HTTP
        //-------------------------------------------------------
        http: {
            headers: {
                'Accept': 'application/x.usb.v2+json',
                'Content-Type': 'application/json',
                'X-Request-Intent': 'cms'
            }
        },

        events: {
            sortingEnded: function(from, to, type, subModels) {
                if(from == to){
                    return;
                }

                var models = subModels ? subModels : this.models;

                models[from].position = to;
                models.move(from, to);
                
                for(var i=0;i < models.length; i++){
                    models[i].position = i;
                }

                var json = JSON.stringify(models);
                this.sortList(json);
            }
        },
        //----------------------------------------------------------
        // Methods
        //-------------------------------------------------------
        methods: {
            toggleFilter: function(){
                this.showFilter = !this.showFilter;
            },

            nextPage: function(){
                if(this.page < this.pageCount){
                    this.page++;
                }
            },
            prevPage: function(){
                if(this.page > 1){
                    this.page--;
                }
            },

            getModels: function(endpoint){
                var call = endpoint + this.queryParams;
                this.$http.get(call).then(
                    function(response){
                        var rd = response.data.data;
                        this.$set('loading', false);

                        for (i = 0; i < rd.length; i++) {
                            this.models.push(rd[i]);
                        }

                        var pagination = response.data.meta.pagination;
                        this.$set('pagination', pagination);
                        if(pagination.links.next){
                           this.getModels(endpoint);
                        } else {
                            this.$set('message', "");
                            this.$set('errorBag', []);
                        }
                   
                    }, 
                    function(response){
                        this.$set('loading', false);
                        this.$set('errorBag', response.data.errors);
                        this.$set('message', response.data.message);
                });
            },

            publish: function(item){
                var action = item.is_published ? 1:0;
                this.$http.put(item.links.resource_uri + '/publish/' + action).then(
                    function(response){                        
                        this.$set('message', response.data.message);
                        alertify.success(response.data.message);
                    }, 
                    function(response){
                        console.log("Error:", response);
                        item.is_published = !item.is_published;
                        this.$set('message', response.data.message);
                        alertify.error(response.data.message);
                });
            },

            delete: function(item, index, parentArray){
                this.$http.delete(item.links.resource_uri).then(
                    function(response){
                        if(parentArray){
                            parentArray.$remove(item);
                        } else {
                            this.models.$remove(item);
                        }
                        this.$set('message', response.data.message);
                        alertify.success(response.data.message);
                    }, 
                    function(response){
                        console.log(response);
                        this.$set('message', response.data.message);
                        alertify.error(response.data.message);
                });
            },

            sortList: function(jsonArray){
                this.$http.put(this.endpoint+'/sort', jsonArray).then(
                    function(response){
                        this.$set('message', response.data.message);
                        alertify.success(response.data.message);
                    }, 
                    function(response){
                        this.$set('errorBag', response.data.errors);
                        this.$set('message', response.data.message);
                        alertify.error(response.data.message);
                });
            },

            pluck: function(key, keyToExtract){
                if (!(key in this.model)){
                    return [];
                }
                return this.model[key].map(function (item) {
                    return item[keyToExtract];
                });
            },
        },

        //----------------------------------------------------------
        // Watching
        //-------------------------------------------------------
        watch: {
            'search': function(value, prev){
                if(value.length > prev.length){
                    this.page = 1;
                }
            },
            'pageSize': function(value, prev){
                if(value > prev){
                    this.page = 1;
                }
            }
        }
    });
}