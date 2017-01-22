<template>
<div>
    <ul v-if='hasError' class='error'>
        <li v-for='err in typeErrors'>
            <span class="help-block">{{ err }}</span>
        </li>
    </ul>
</div>
</template>

<script>
    export default {
        props: [
            'property',
            'errors',
        ],

        mounted() {},

        computed: {

            typeErrors() {
                return this.errors[this.property];
            },

            hasError() {
                return Boolean(this.errors && this.errors[this.property]);
            },

            formClass() {
                return [
                    { "has-error": this.hasError }
                ];
            },
        }
    }
</script>
