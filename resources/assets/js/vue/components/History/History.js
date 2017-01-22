
export default {
    props: ['history', 'projectId'],

    data () {
        return {
            aHistory: null,
            loading: true
        }
    },

    ready() {
    },

    methods: {
        getHistory (history) {
            this.loading = true;
            var endpoint = '/api/projects/'+this.projectId+'/history/'+history.id;

            this.$http.get(endpoint).then((response)=>{
                this.aHistory = response.data.data;
                this.loading = false;
            }, (response)=>{
                console.log("error", response);
                this.loading = false;
            });
        },

        closeModal () {
            this.aHistory = {};
        }
    }
}



