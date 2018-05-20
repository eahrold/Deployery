<style scoped lang="scss">
.slide-toggle {
    position: relative;
    height: 90px;
    width: 100%;
    overflow: hidden;
}

.bordered {
    border: 1px solid grey
}

.buttons-wrapper {
    transition: .3s all ease-out;
    position: absolute;
    right: 0;
    width: 50px;
    top: 0;
    bottom: 0;

    &.active {
        width: 100%;
        // left: 0;
        right: 0;
    }

    .details {
        padding-left: 25px;
        background-color: white;
    }
}

.button,
.buttons {
    width: 50px;
    color: white;
}

.flex-center-all {
    display: -webkit-box !important;
    display: -ms-flexbox !important;
    display: flex !important;

    -webkit-box-pack: center !important;
    -ms-flex-pack: center !important;
    justify-content: center !important;

    -webkit-box-align: center !important;
    -ms-flex-align: center !important;
    align-items: center !important;
}

.pr__ext6 {
    padding-right: 2.5em;
}

.commit-message {

}
</style>

<template>
    <div>
        <div class="slide-toggle bordered d-flex flex-row">
            <div class="col d-flex flex-column justify-content-start m-2 pr__ext6 align-items-start">
                <div v-if='commit && commit.hash'>
                    <div>
                        <b>{{ title }}</b>
                        <code class="mx-2">{{ commit.hash }}</code>
                        <small>{{ localTime(commit.date) }}</small>
                    </div>
                    <small class="commit-message">{{ commit.message | truncate(300) }}</small>
                </div>
                <div v-else>
                    <div><b>No Commit Selected</b></div>
                </div>
            </div>

            <div
                :class="{active,}"
                class="buttons-wrapper d-flex">
                <div class="buttons d-flex flex-column">
                    <div
                        class="col button bg-info flex-center-all"
                        @click="choose(`select`)">
                        <i
                            v-if="active===`select`"
                            class="fa fa-close"
                            aria-hidden="true"/>
                        <i
                            v-else
                            class="fa fa-list-ul"
                            aria-hidden="true"/>
                    </div>
                    <div
                        class="col button bg-primary flex-center-all"
                        @click="choose(`enter`)">
                        <i
                            v-if="active===`enter`"
                            class="fa fa-close"
                            aria-hidden="true"/>
                        <i
                            v-else
                            class="fa fa-terminal"
                            aria-hidden="true"/>
                    </div>
                </div>

                <div class="col details d-flex align-items-center">
                    <!-- Type Commit -->
                    <template v-if="active===`enter`">
                        <div class="col-12">
                            <form class='input-group needs-validation' @submit.prevent='queryServerForCommit' novalidate>
                                <input
                                    :class="{'is-invalid': !!searchErrors}"
                                    class="form-control"
                                    v-model.trim='searchCommit'
                                    placeholder="enter the commit ref">
                                <div class="input-group-append">
                                    <button type='submit' class="btn btn-info">
                                        <i class="fa" :class='searchClass' aria-hidden="true"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </template>

                    <!-- Commit Dropdown -->
                    <template v-else-if="active===`select`">
                        <div class="col-12">
                            <div class="select-list">
                                <select
                                    class="form-control"
                                    v-model='aCommit'>
                                    <option
                                        v-for="(commit, idx) in commits"
                                        :key="idx"
                                        :disabled='commit.hash === aCommit.hash'
                                        :value="commit">{{ commit | formatCommit(aCommit) }}</option>
                                </select>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
        <div class="text-danger text-right">
            <small>{{ searchErrors ? searchErrors.message : "&nbsp;" }}</small>
        </div>
    </div>
</template>

<script type="text/javascript">

import _ from 'lodash'

export default {
    // ----------------------------------------------------------
    // Template Dependencies
    // -------------------------------------------------------
    // components: {},
    // directives: {},
    filters: {
        formatCommit: function(commit, current){
            let { message, hash } = commit
            message = message.substring(0, 60) + (message.length > 80 ? '...' : "")
            return `${hash} : ${message}`
        }
    },

    // ----------------------------------------------------------
    // Composition
    // -------------------------------------------------------
    mixins: [],
    props: {
        title: {
            type: String,
            required: true
        },

        commit: {
            type: Object,
            required: false,
            default: null
        },

        commits: {
            type: Array,
            required: false,
            default: null
        },

        findCommit: {
            type: Function,
            required: true,
        },
    },

    // ----------------------------------------------------------
    // Local State
    // -------------------------------------------------------
    data () {
        return {
            aCommit: null,
            active: null,
            selected: null,

            searching: false,
            searchCommit: null,
            searchErrors: null,
        }
    },

    computed: {
        searchClass() {
            return this.searching ? 'fa-spinner fa-spin' : 'fa-search'
        }
    },

    // ----------------------------------------------------------
    // Events
    // -------------------------------------------------------
    watch: {
        commit(commit) {
            this.aCommit = commit
        },

        aCommit(aCommit) {
            console.log({aCommit})
            this.$emit(`update:commit`, aCommit)
        },

        active(active) {
            if( ! active) {
                this.reset()
            }
        }
    },

    mounted () {},
    // beforeDestroy() { /* dealloc anything you need to here*/ },

    // ----------------------------------------------------------
    // Non-Reactive Properties
    // -------------------------------------------------------
    methods: {
        reset() {
            this.searchErrors = null
        },

        choose (type) {
            if (this.active === type) {
                this.active = null
            }
            else this.active = type
        },

        queryServerForCommit() {
            this.searching = true;
            this.findCommit(this.searchCommit).then((response)=>{
                this.aCommit = response.data
                this.active = false;
            }).catch((response)=>{
                const { message } = response.data
                this.searchErrors = { message, }
            }).then(()=>{
                this.searching = false
            })
        },
    }
}
</script>
