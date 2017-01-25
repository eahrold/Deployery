var moment = require('moment');

export default {
    props: ['projectId'],

    data () {
        return {
            history: [],
            ready: false,
            aHistory: null,
            loading: true
        }
    },

    mounted () {
        this.load();
        this.listen();
    },

    methods: {
        load () {
            var ready = false;
            var endpoint = this.$parent.endpoint + '/history';
            // console.log("calling", endpoint);
             this.$http.get(endpoint).then((response)=>{
                this.history = response.data.data;
                this.ready = true;
            }, (response)=>{
                this.ready = true;
                console.error("error", response);
            });
        },

        listen (oldRoute) {
            if (oldRoute) {
                echo.leave('project.'+oldRoute.params.id);
            }
            echo.private('project.'+ this.$route.params.id)
                .listen('HistoryCreatedEvent', this.handleHistoryCreated);
        },

        handleHistoryCreated(data){
            this.history.unshift(data.history);
        },

        getHistory (history) {
            this.loading = true;
            var endpoint = this.$parent.endpoint + '/history/'+ history.id;

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
    },

    watch: {
        $route (newRoute, oldRoute) {
            this.listen(oldRoute);
            this.load();
        }
    },
}



