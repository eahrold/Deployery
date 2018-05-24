<template>
<form-modal @close='exit'>
    <template slot='body'>
    <form-text v-model='model.username' property='username' :errors='errors' :required='true'></form-text>
    <form-text v-model='model.email' property='email' :rules='$validation.rules.email' :errors='errors' :required='true'></form-text>
    <form-text v-model='model.first_name' property='first_name' :errors='errors' :required='true'></form-text>
    <form-text v-model='model.last_name' property='last_name' :errors='errors' :required='true'></form-text>

    <form-password
        v-model='model.password'
        property='password'
        :errors='errors'>
    </form-password>


    <form-password
        v-model='model.password_confirm'
        property='password_confirm'
        :confirm='model.password'
        :rules='[$validation.rules.match(model.password, "password")]'
        :errors='errors'>
    </form-password>

    <form-checkbox v-model='model.is_admin' property='is_admin' :errors='errors'></form-checkbox>
    <form-checkbox v-model='model.can_manage_teams' property='can_manage_teams' :errors='errors'></form-checkbox>
    <form-checkbox v-model='model.can_join_teams' property='can_join_teams' :errors='errors'></form-checkbox>
    </template>

    <template slot='footer'>
        <form-save-button :disabled='$validation.fails' @save='submit'></form-save-button>
    </template>
</form-modal>
</template>

<script type="text/javascript">

import _ from 'lodash'

let VM_FROM = null;

export default {
    //----------------------------------------------------------
    // Template Dependencies
    //-------------------------------------------------------
    // components: {},
    // directives: {},
    // filters: {},

    //----------------------------------------------------------
    // Composition
    //-------------------------------------------------------
    mixins: [],
    props: {},

    //----------------------------------------------------------
    // Local State
    //-------------------------------------------------------
    data() {
        return {
            errors: null,
            model: {
                username: null,
                email: null,
                first_name: null,
                last_name: null,
            }
        }
    },

    computed: {

    },

    //----------------------------------------------------------
    // Events
    //-------------------------------------------------------
    // watch: {},
    mounted() {
        this.load();
    },
    // beforeDestroy() { /* dealloc anything you need to here*/ },

    beforeRouteEnter (to, from, next) {
        VM_FROM = from
        next()
    },

    beforeRouteLeave (to, from, next) {
        VM_FROM = null
        next()
    },

    //----------------------------------------------------------
    // Non-Reactive Properties
    //-------------------------------------------------------
    methods: {
        load() {
            this.$http.get('/api/my-account').then(response=>{
                this.model = response.data.data
            })
        },

        exit() {
            if(VM_FROM)this.$router.go(-1)
            else this.$router.push({path: '/'})
        },

        submit() {
            this.$http.put('/api/my-account', this.model).then(response=>{
                this.model = response.data.data
                this.$vfalert.confirm("Do you want to continue?", 'success').then(()=>{
                    // Continue
                }).catch(()=>{
                    this.exit()
                })

            }).catch(error=>{
                this.$vfalert.errorResponse(error.response)
            })
        }
    },
}
</script>

<style scoped lang="scss"></style>
