export default {
    props: ['scripts', 'projectId'],
    ready() { },
    methods: {
        getEndpoint: function(idOrVerb, verb){
            verb = verb ? '/'+verb : '';
            return '/projects/'+this.projectId+'/scripts/'+idOrVerb+verb
        }
    }
}