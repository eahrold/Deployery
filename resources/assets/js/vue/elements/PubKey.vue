<template>
    <form-card>
        <div slot="header">
            <i class="fa fa-clipboard clipboard"
                aria-hidden="true"
                data-clipboard-action="copy"
                data-clipboard-target='#pubkey'>
            </i>
            <span>{{ heading }}</span>
        </div>
        <code id='pubkey' class='pubkey'>{{ pubKey }}</code>
    </form-card>
</template>

<script type="text/javascript">

var Clipboard = require('clipboard');

export default {
    name: "PubKey",

    //----------------------------------------------------------
    // Template Dependencies
    //-------------------------------------------------------
    // components: {},
    // directives: {},
    // filters: {},

    //----------------------------------------------------------
    // Composition
    //-------------------------------------------------------
    // mixins: [],
    props: {
        heading: {
            type: String,
        },
        pubKey: {
            type: String
        }

    },


    //----------------------------------------------------------
    // Events
    //-------------------------------------------------------
    // watch: {},
    mounted() {
        var clipboard = new Clipboard('.clipboard');
        clipboard.on('success', (e)=>{
            this.setTooltip(e.trigger, 'Copied!');
            this.hideTooltip(e.trigger);
        }).on('error', (e)=>{
            this.setTooltip(e.trigger, 'Press Ctrl-C to copy');
            this.hideTooltip(e.trigger);
        })

        $('.clipboard').tooltip({
            trigger: 'click',
            placement: 'bottom'
        });
    },

    //----------------------------------------------------------
    // Non-Reactive Properties
    //-------------------------------------------------------
    methods: {
        setTooltip(btn, message){
          $(btn).attr('data-original-title', message)
                .tooltip('show');
        },

        hideTooltip(btn){
          setTimeout(function() {
            $(btn).tooltip('hide');
          }, 3000);
        },
    },
}
</script>

<style scoped lang="scss">
.pubkey {
    word-break: break-all;
}
</style>
