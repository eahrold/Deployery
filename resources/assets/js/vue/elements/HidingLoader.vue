<style scoped lang="scss">
.hidden-loader-enter-active, .hidden-loader-leave-active {
  transition: all .5s ease;
}

.hidden-loader-enter, .hidden-loader-leave-active {
  opacity: 0;
  transform: scale(0);
}

.hidden_loader {
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    margin: auto;


    width: 30vw;
    max-width: 80vh;

    height: 30vw;
    max-height: 80vh;

    z-index: 9999;
    background: grey;

    border-radius: 50%;
    box-shadow: 5px 10px 18px rgba(0, 0, 0, 0.5);

    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
}

</style>

<template>
    <transition name='hidden-loader'>
        <div v-if='show' :style='style' class='hidden_loader'>
            <div class="hidden_loader_content">
                <slot><h4>Loading</h4></slot>
            </div>
        </div>
    </transition>
</template>

<script type="text/javascript">

// import _ from 'lodash'

export default {
    name: "hiding-loader",

    props: {
        backgroundColor: {
            type: String,
            default: 'grey',
        },

        color: {
            type: String,
            default: 'white',
        },

        show: {
            type: Boolean,
            default: true,
        }
    },

    computed: {
        style() {
            return {
                backgroundColor: this.backgroundColor,
                color: this.color,
            }
        }
    }

}
</script>

