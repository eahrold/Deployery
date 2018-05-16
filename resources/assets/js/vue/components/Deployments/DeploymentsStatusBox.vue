<template>
<div class="stage-box-wrapper justify-contents-center text-center" :class='{complete, }'>
    <div class="col stage-box-sized">
        <div class="stage-box text-center" :class='{complete, active, }'></div>
        <div class="stage-box-text" :class='{complete, active, }'>
            <transition name='fade' mode='out-in'>
                <i v-if='complete' class="pulse fa fa-check" aria-hidden="true"></i>
                <small v-else>{{ description }}</small>
            </transition>
        </div>
    </div>
    <div class="col">
        <small class="description">{{ description }}</small>
    </div>
</div>
</template>

<script type="text/javascript">

const STAGE_PREPARING = 0;
const STAGE_PRE_INSTALL = 1;
const STAGE_SYNC_FILES = 2;
const STAGE_SYNC_FILES_COMPLETE = 3;
const STAGE_WRITING_CONFIG = 4;
const STAGE_POST_INSTALL = 5;
const STAGE_COMPLETE = 6;

const descriptions = {
    [STAGE_PREPARING]: 'Preping',
    [STAGE_PRE_INSTALL]: 'Preinstall',
    [STAGE_SYNC_FILES]: 'Syncing',
    [STAGE_WRITING_CONFIG]: 'Configs',
    [STAGE_POST_INSTALL]: 'Postinstall',
    [STAGE_COMPLETE]: 'Complete',
}
import _ from 'lodash'

export default {
    mixins: [],
    props: {
        stage: {
            type: Number,
            required: true
        },

        activeStage: {
            type: Number,
            required: true
        }
    },

    computed: {
        description() {
            return _.get(descriptions, this.stage, 'N/A')
        },

        complete() {
            return this.activeStage > this.stage
        },

        active() {
            return this.stage == this.activeStage
        }
    },
}
</script>

<style scoped lang="scss">
$box-size: 74px;
$spinnerColorHilight: rgba(0, 32, 255, 1);
$spinnerColor: rgba(0, 32, 255, .7);
$borderSize: 2px;

$colorBlue: blue;
$colorGreen: green;

.stage-box-sized {
    position: relative;
    margin: auto;
    width: $box-size;
    height: $box-size;
}

.stage-box,
.stage-box-text {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    margin: auto;
    width: $box-size;
    height: $box-size;
    line-height: $box-size;
    opacity: .5;

     &.complete {
        color: $colorGreen;
        border-color: $colorGreen;
        opacity: 1;
    }
}

.stage-box {
    transition: all ease-out 1s;
    border: $borderSize solid gray;
    border-radius: 50%;
    opacity: .5;

     &.complete {
        opacity: 1;
        color: $colorGreen;
        border-color: $colorGreen;
     }

    &.active {
        opacity: 1;
        color: $colorBlue;

        border-top: $borderSize solid $spinnerColor;
        border-right: $borderSize solid $spinnerColor;
        border-bottom: $borderSize solid $spinnerColor;
        border-left: $borderSize solid $spinnerColorHilight;

        -webkit-transform: translateZ(0);
        -ms-transform: translateZ(0);
        transform: translateZ(0);
        -webkit-animation: spinActiveAnimation 1.1s infinite linear;
        animation: spinActiveAnimation 1.1s infinite linear;
    }
}

.stage-box-wrapper {
    color: black;
    position: relative;
    width: $box-size + 16;
    height: $box-size + 16;

    .description {
        opacity: 0;
    }

    &.complete:hover {
        .description {
            opacity: .5;
        }
    }
}

.stage-box,
.stage-box:after {
  border-radius: 50%;
}

.pulse {
  animation-name: pulse;
  animation-duration: .3s;
}

@keyframes pulse {
  0% {transform: scale(1);}
  50% {transform: scale(1.5);}
  100% {transform: scale(1);}
}

@-webkit-keyframes spinActiveAnimation {
  0% {
    -webkit-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@keyframes spinActiveAnimation {
  0% {
    -webkit-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}

</style>

