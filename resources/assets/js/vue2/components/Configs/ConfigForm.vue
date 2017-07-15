<template>
    <div class="modal fade" id="configForm" role="dialog">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    {{ heading }}
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class='form-group' v-if='model'>

                        <div class='panel panel-default'>
                            <div class="panel-heading">Config</div>
                            <div class='panel-body'>
                                <form-text v-model='model.path' :errors='errors' property="path"></form-text>
                                <form-textarea v-model='model.contents' :rows='10' :errors='errors' property="contents"></form-textarea>
                            </div>
                        </div>

                        <div class='panel panel-default'>
                            <div class="panel-heading">Servers</div>
                            <div class='panel-body'>
                                <div v-for='(name, key) in servers'>
                                    <input type="checkbox" :id="key" :value="key" v-model="model.server_ids">
                                    <label :for="key">{{ name }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <form-save-button :saving='saving' :is-dirty='isDirty' @save='save'></form-save-button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { form, modalForm } from "../../mixins/AdminForm.js";

    export default {
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

            type () {
                return 'configs';
            },

            servers () {
                return _.get(this.options, 'servers', []);
            }
        }
    }
</script>