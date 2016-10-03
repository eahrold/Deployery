export default {
    props: ['configs', 'projectId'],
    ready() {},
    methods: {
        getEndpoint: function(idOrVerb, verb){
            verb = verb ? '/' + verb : '';
            return '/projects/'+this.projectId+'/configs/'+idOrVerb+verb
        }
    }
}
