<template>
<div class="container container-lg">
    <transition name='fade'>
     <div class="col-md-12" v-if='deployment.deploying' v-cloak>
        <div class="panel panel-default">
            <div class="pannel-nav navbar navbar-default navbar-static">
                <div class='nav navbar-nav navbar-left'>
                    {{ deployingStatusMessage }}
                </div>
            </div>

            <div class='panel-body'>
                {{ deployingCurrentMessage }}
            </div>
        </div>
    </div>
    </transition>
</div>
</template>

<script type="text/javascript">

import _ from 'lodash'
import { mapGetters, mapState } from 'vuex';


export default {

    computed: {
        ...mapState(['deployment']),
        ...mapGetters([
            'deployingServerId',
            'deployingServerName',
            'deployingCurrentMessage',
            'deployingStatusMessage'
        ]),
    },
}
</script>

<style scoped lang="scss"></style>
