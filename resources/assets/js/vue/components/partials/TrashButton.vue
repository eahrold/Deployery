<template>
    <button class="clear-form delete icon" @click='trash()'>
        <i class="fa fa-trash-o" aria-hidden="true"></i>
    </button>
</template>

<script>
    export default {
        props: ['object', 'type'],
        methods: {
            trash: function(){
                this.$dispatch('delete-item', this.object, this.type);
            }
        }
    }
</script>
