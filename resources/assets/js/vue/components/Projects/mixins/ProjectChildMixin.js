export default {
    props: {
        project: {
            type: Object,
            required: true
        },
        loading: {
            type: Boolean,
            default: false,
        }
    },

    computed: {
        projectId() {
            return _.get(this.$route, 'params.project_id')
        },
    },

    methods: {
        relatedItemUpdated(model, type) {
            let newVal = _.get(this.project, type, [])

            const idx = _.findIndex(newVal, {id: model.id})
            if(idx !== -1){
                newVal.splice(idx, 1, model);
            } else {
                newVal.push(model);
            }
            let project = _.assign({}, this.project, {[type]: newVal})
            this.$dispatch('PROJECT_SET', project)
        }
    }
}