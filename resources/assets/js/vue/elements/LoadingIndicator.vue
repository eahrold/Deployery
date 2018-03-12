<template>
    <div v-if='loading' class="col-md-12 text-center loading-container">
        <div class="loader"></div>
    </div>
</template>

<script>
    export default {
        props: {
            loading: {
                type: Boolean,
                default: true
            },
        },
    }
</script>

<style lang='scss' scoped>
@import '../../../sass/variables.scss';

.loading-container {
    min-height: 40vh;
}


</style>