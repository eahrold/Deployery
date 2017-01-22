<template>
    <button class="clear-form delete icon" @click='trash()'>
        <i class="fa fa-trash-o" aria-hidden="true"></i>
    </button>
</template>

<script>
    export default {
        props: {
            object: {
                type: Object,
                required: true
            },
            type: {
                type: String,
                required: true
            }
        },

        methods: {
            trash: function(){
                bus.$emit('delete-project-item', this.object, this.type);
            }
        }
    }
</script>
