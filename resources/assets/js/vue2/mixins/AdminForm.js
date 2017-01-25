var modalForm = {
    mounted () {
        this.$nextTick(()=>{
            this.prepareModal();
        })
    },

    methods : {
        prepareModal() {
            var modal = $(this.$el);
            var self = this;
            modal.on('hide.bs.modal',(e)=>{
                if(!this.isPristine && !confirm("You have unsaved data, continue?")){
                    e.preventDefault();
                }
            });

            modal.on('hidden.bs.modal',(e)=>{
                this.model = this.pristine = null;
            });

            modal.on('show.bs.modal',(e)=>{
                var id = $(e.relatedTarget).data('model-id');
                return this.load(id);
            });
        },

        closeModal() {
            $(this.$el).modal('hide');
        }
    }
}

var form = {

    data () {
        return {
            model: null,
            errors: {},
            message: '',
            options: {},
            pristine: null,
            saving: false,
            isNew: false,
            loading: false
        }
    },

    computed : {
        isDirty () {
            return (this.model && !this.model.id) || !this.isPristine;
        },

        isPristine () {
            var diff = _.omitBy(this.model, (v, k) => {
                var obj = this.pristine[k];
                var val = v;

                if(typeof obj === 'object')  {
                    return _.isEmpty(_.omitBy(obj, (k, v) => {
                        return val[v] === obj[v];
                    }));
                }

                return obj === v;
            });

            console.log("ojbect diff", diff, this.model, this.pristine);

            return _.isEmpty(diff);
        },

        heading () {
            if(this.loading) {
                return "Loading...";
            }
            var updating = "Updating " + _.get(this, 'model.name', this.type || "");
            return Boolean(_.get(this.model, 'id')) ? updating : ("");
        },

        apiEndpoint () {
            return this.endpoint + (this.model.id ? "/" + this.model.id : "");
        },

        errorsHtml () {
            var msg = '<h4>'+this.message+'</h4><ul class="list-unstyled">';
            if (this.errors) {
                _.each(this.errors, (e)=>{
                    msg += '<li>'+e+'<li>'
                });
            }
            msg +='</ul>';
            return msg;
        },
    },

    methods : {
        finally (response) {
            this.errors = response.data.errors;
            this.message = response.data.message;
            this.saving = false;
            this.loading = false;
        },

        success (response) {
            var data = this.makePristine(response.data.data);
            if(this.isNew) {
                bus.$emit('add-project-item', data, this.type);
                this.isNew = false;
            }
            this.finally(response);
        },

        failure (response) {
            console.log('success');

            this.finally(response);
            this.presentError();
        },

        presentError () {
            Alerter.error(this.errorsHtml);
        },

        makePristine (data) {
            var model = this.model = data || {};
            this.pristine = JSON.parse(JSON.stringify(model));
            return model;
        },

        load (id) {
            // Get the select options
            this.loading = true;
            this.$http.get(this.endpoint+'/options').then(
                (response)=>{
                    this.options = _.get(response.data, 'options', {});
            });

            if (id) {
                // Get the model
                this.$http.get(this.endpoint+'/'+id).then(
                    (response)=>{
                        this.isNew = false;
                        this.success(response);
                    },
                    (response)=>{
                    // Handle Error getting model 401...
                });
            } else {
                this.loading = false;
                this.makePristine({});
            }
        },

        save () {
            this.saving = true;
            if(this.model.id) {
                this.isNew = true;
                this.$http.put(this.apiEndpoint, this.model).then(this.success, this.failure);
            } else {
                this.isNew = true;
                this.$http.post(this.apiEndpoint, this.model).then(this.success, this.failure);
            }
        },

        close () {
            if(!this.isPristine && !confirm("You have unsaved data, continue?")){
                return;
            }
            this.$emit('close', this.model);
        }
    }
}

export { form, modalForm };
