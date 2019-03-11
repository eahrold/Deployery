<template>
<form-modal @close='close'>
    <template slot="header">
        <b class="ml-3">{{ header }}</b>
    </template>

    <div slot="body" :class="{loading: loading}">
        <template v-if='model'>
            <form-section heading='Config'>
                    <form-text v-model='model.path' :errors='errors' property="path"></form-text>
                    <form-textarea v-model='model.contents' :rows='10' :errors='errors' property="contents"></form-textarea>
            </form-section>

            <form-section heading='Servers'>
                <div v-for='(name, key) in servers'>
                    <input type="checkbox" :id="key" :value="key" v-model="model.server_ids">
                    <label :for="key">{{ name }}</label>
                </div>
            </form-section>
        </template>
    </div>

    <template slot="footer">
        <form-save-button :saving='saving' :is-dirty='isDirty' @save='save'></form-save-button>
    </template>
</form-modal>
</template>

<script>
import { form, modalForm } from "../../mixins/AdminForm.js";

export default {
    name: 'configs-form',
    mixins: [ form, modalForm ],

    props: {
        endpoint: {
            type: String,
            required: true
        }
    },

    methods : {
        schema () {
            return {
                server_ids: []
            }
        },
    },

    computed : {
        header() {
            return this.loading ? "Loading..." : `Editing Congif File at path: ${this.model.path}`
        },

        type () {
            return 'configs';
        },

        servers () {
            return _.get(this.options, 'servers', []);
        }
    }
}
</script>