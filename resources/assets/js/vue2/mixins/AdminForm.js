var modalForm = {
    mounted () {
        this.$nextTick(()=>{
            this.prepareModal();
        })
    },

    methods : {
        prepareModal() {
            var modal = $(this.$el);

            modal.on('hide.bs.modal',(e)=>{
                console.log('checking pristine');
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
            options: {},
            pristine: null,
            saving: false,
            isNew: false,
        }
    },

    computed : {
        isDirty () {
            return (this.model && !this.model.id) || !this.isPristine;
        },

        isPristine () {
            return Boolean(this.pristine === JSON.stringify(this.model));
        },

        heading () {
            var updating = "Updating " + _.get(this, 'model.name', "the resource");
            return _.get(this, 'model.id') ?  "Updating " + _.get(this, 'model.name', "the resource") : "Adding new";
        },

        apiEndpoint () {
            return this.endpoint + (this.model.id ? "/" + this.model.id : "");
        },

        errorsHtml () {
            var msg = '<ul class="list-unstyled">';
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
            this.saving = false;
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
            this.finally(response);
            this.presentError();
        },

        presentError () {
            swal({title: this.message, text: this.errorsHtml, html: true, type: 'error'});
        },

        makePristine (data) {
            var model = this.model = data || {};
            this.pristine = JSON.stringify(model);
            return model;
        },

        load (id) {
            // Get the select options
            this.$http.get(this.endpoint+'/options').then(
                (response)=>{
                    console.log('opts', _.get(response.data, 'options'));
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
                this.makePristine({});
            }
        },

        save () {
            this.saving = true;
            if(this.model.id) {
                this.isNew = false;
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
