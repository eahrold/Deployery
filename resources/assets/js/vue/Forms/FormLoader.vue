<template>
    <div class='loader-wrapper'>
        <transition name='fade'>
            <div v-if='loading' class='loader'>
                <div class='ring-1'></div>
                <div class='ring-2'></div>
                <div class='ring-3'></div>
            </div>
        </transition>
    </div>
</template>

<script>
    export default {
    props : {
        loading: {
            type: Boolean,
            required: true
        }
    }
}
</script>

<style>
.loader-wrapper {
    height: 24px;
    width: 24px;
    position: relative;
    margin-right: 10px;
    margin-left: 10px;
}

.loading-transition {
  -webkit-transition: all 1s ease;
  transition: all 1s ease;
  /*  height: 30px;
  padding: 10px;*/
  overflow: hidden;
}

.loading-enter, .loading-leave {
  opacity: 0;
}

.loader {
  width: 100%;
  height: auto;
  text-align: center;
}

.loader p {
  color: #158CA0;
  font-size: 1em;
  text-transform: uppercase;
}

.loader div {
  display: block;
  position: absolute;
  top: 50%;
  left: 50%;
  border-radius: 50%;
  background-color: transparent;
  box-shadow: inset 0 0 0 0.5em transparent;
  background: transparent;
  background-blend-mode: multiply;
  border-top: 2px solid #158CA0;
  border-left: 2px solid #B1C291;
  border-bottom: 2px solid #FD5653;
  border-right: 2px solid #FFD15B;
}

.loader div.ring-1 {
  height: 10px;
  width: 10px;
  margin-top: -5px;
  margin-left: -5px;
  -webkit-animation: roto 2s infinite linear;
  animation: roto 2s infinite linear;
}

.loader div.ring-2 {
  height: 16px;
  width: 16px;
  margin-top: -8px;
  margin-left: -8px;
  -webkit-animation: rotob 3s infinite cubic-bezier(0.1, 1.15, 1, 1);
  animation: rotob 3s infinite cubic-bezier(0.1, 1.15, 1, 1);
}

.loader div.ring-3 {
  height: 22px;
  width: 22px;
  margin-top: -11px;
  margin-left: -11px;
  -webkit-animation: roto 1s infinite linear;
  animation: roto 1s infinite linear;
}

@-webkit-keyframes roto {
  0% {
    -webkit-transform: rotateZ(0deg);
    transform: rotateZ(0deg);
  }
  100% {
    -webkit-transform: rotateZ(360deg);
    transform: rotateZ(360deg);
  }
}

@keyframes roto {
  0% {
    -webkit-transform: rotateZ(0deg);
    transform: rotateZ(0deg);
  }
  100% {
    -webkit-transform: rotateZ(360deg);
    transform: rotateZ(360deg);
  }
}

@-webkit-keyframes rotob {
  0% {
    -webkit-transform: rotateZ(0deg);
    transform: rotateZ(0deg);
  }
  100% {
    -webkit-transform: rotateZ(-360deg);
    transform: rotateZ(-360deg);
  }
}

@keyframes rotob {
  0% {
    -webkit-transform: rotateZ(0deg);
    transform: rotateZ(0deg);
  }
  100% {
    -webkit-transform: rotateZ(-360deg);
    transform: rotateZ(-360deg);
  }
}
</style>