<template>
    <div class="vf-form-section">
        <div class="row">
            <div class="col">
                <h4 class='vf-section-title'>
                    <slot name='header'>{{ header }}</slot>
                </h4>
            </div>
        </div>
        <div class="row">
            <div class="col">
            <slot></slot>
            </div>
        </div>
    </div>
</template>

<script type="text/javascript">
export default {
    props: {
        header: {
            type: String,
            required: false
        }
    }
}
</script>

<style scoped>
.vf-form-section {
    margin-top: 1em;
}

.vf-form-section > .row {
    margin-left: 0;
    margin-right: 0
}

.vf-form-section .vf-section-title {
    width: 100%;
    padding-bottom: 1em;
    border-bottom: 2px solid #eeeeee;
}
</style>
