export const modalForm = {
    mounted () {
        this.load(this.$route.params.id)
    },
}

export const form = {
    beforeRouteEnter (to, from, next) {
        next(vm => {
            vm.$_AdminForm__from_route = from;
        })
    },

    data () {
        return {
            model: this.schema(),
            errors: {},
            message: '',
            options: {},
            pristine: null,
            saving: false,
            isNew: false,
            loading: false,
            $_AdminForm__from_route: null,
        }
    },

    computed : {
        $_AdminForm__closeMessage() {
            return "Unsaved changes to: " + this.$_AdminForm__dirtyProps.join(', ') + ". Do you still want to close?"
        },

        $_AdminForm__dirtyProps() {
            const a = this.model;
            const b = this.pristine;

            return _.reduce(a, function(result, value, key) {
                var bVal = b[key];
                if(_.isObject(value)) {
                    var bCmp = _.pickBy(bVal);
                    var aCmp = _.pickBy(value);
                    return _.isEqual(aCmp, bCmp) ? result : _.uniq(result.concat(key));
                }
                return _.isEqual(value, bVal) ? result : result.concat(key);
            }, []);
        },

        isDirty () {
            return (this.model && !this.model.id) || !this.isPristine;
        },

        isPristine () {
            return _.isEmpty(
                _.difference(this.$_AdminForm__dirtyProps,
                    _.get(this, 'ignoreDirty', [])
                )
            );
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

        saved (response) {
            const options = {
                confirmText: "Close",
                cancelText: "Keep Working"
            }
            this.$vfalert.confirm("Do you want to Keep working on this, or close", 'success', options)
            .then(this.$_AdminForm_navigateBack)
            .catch(()=>{
                this.success(response)
            })
        },

        failure ({response}) {
            this.finally(response);
            this.presentError();
        },

        presentError () {
            this.$alerter.error(this.errorsHtml);
        },

        schema () {
            return {};
        },

        makePristine (data) {
            var model = this.model = data || this.schema();
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

            if (id !== 'create' && id !== undefined) {
                // Get the model
                this.$http.get(this.endpoint+'/'+id).then(
                    (response)=>{
                        this.isNew = false;
                        this.success(response);
                    },
                    ({response})=>{
                    // Handle Error getting model 401...
                });
            } else {
                this.loading = false;
                this.makePristine(this.schema());
            }
        },

        save () {
            this.saving = true;
            if(this.model.id) {
                this.isNew = true;
                this.$http.put(this.apiEndpoint, this.model).then(this.saved, this.failure);
            } else {
                this.isNew = true;
                this.$http.post(this.apiEndpoint, this.model).then(this.saved, this.failure);
            }
        },

        close () {
            if(this.isDirty && !confirm(this.$_AdminForm__closeMessage)){
                return
            }
            this.$_AdminForm_navigateBack();
        },

        $_AdminForm_navigateBack() {
            const canReturn = _.get(this.$_AdminForm__from_route, 'meta.canReturn');
            if (!_.isEmpty(this.$_AdminForm__from_route.name) && canReturn !== false) {
                this.$router.go(-1);
                return;
            }

            const name = _.get(this.$route, 'meta.back');
            if (name) {
                this.$router.push({name: name});
            } else {
                this.$router.push(this.$route.path.split('/').slice(0, -1).join('/'));
            }
        },
    }
}
