<template>
    <div class="form-group row">
        <div class="col-md-12 text-right">
            <div @click='save()'
                class="btn btn-primary btn-save"
                :class="{ 'active': saving }"
                :disabled="saving">
                <i v-if='saving' class="fa fa-spinner fa-spin"></i>
                {{ (saving ? "Saving..." : ( isDirty ? "Save" : "Unchanged")) }}
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props : {
            isDirty: {
                type: Boolean,
                default: true
            },

            saving: {
                type: Boolean,
                default: false
            }
        },

        data () {
            return {
                exit: false
            }
        },

        methods : {
            save() {
                this.$emit('save', this.exit);
            }
        }
    }
</script>

<style>
    .btn.btn-save {
        min-width: 110px;
    }
</style>