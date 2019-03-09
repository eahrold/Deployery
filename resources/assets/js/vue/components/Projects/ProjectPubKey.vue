<template>
<form-card>
    <div slot="header">
        <i class="fa fa-clipboard clipboard"
            aria-hidden="true"
            data-clipboard-action="copy"
            data-clipboard-target='#pubkey'>
        </i>
        <span>Add this key to your GitHub/BitBucket repo host</span>
    </div>
    <code id='pubkey' class='pubkey'>{{ userPubKey }}</code>
</form-card>
</template>

<script type="text/javascript">

import _ from 'lodash'
var Clipboard = require('clipboard');

export default {
    mounted () {
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

    methods : {

        setTooltip(btn, message){
          $(btn).attr('data-original-title', message)
                .tooltip('show');
        },

        hideTooltip(btn){
          setTimeout(function() {
            $(btn).tooltip('hide');
          }, 3000);
        },

        success (response) {
            window.location = '/projects/'+response.data.data.id + '/info';
        },
    },

    computed : {
        userPubKey () {
            return _.get(window.Deployery, 'userPubKey', '');
        }
    }
}
</script>

<style scoped lang="scss">
.pubkey {
    word-break: break-all;
}
</style>
