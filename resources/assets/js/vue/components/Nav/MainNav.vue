<template>
 <div class="collapse navbar-collapse" id="app-navbar-collapse">

    <ul class="navbar-nav">
        <template v-if='!isGuest'>
        <li class="nav-item">
            <router-link v-if='isVueRoute' class='nav-link' :to="{ name: 'projects.dashbaord'}">Dashbaord</router-link>
            <a v-else class='nav-link' href='/'>Dashbaord</a>
        </li>

        <li class="nav-item dropdown">
            <a href="#"
                class="nav-link dropdown-toggle"
                data-toggle="dropdown"
                role="button"
                aria-expanded="false">
                Projects
            </a>
            <ul class="dropdown-menu" role="menu">
                <li v-for='(project, idx) in projects' class="dropdown-item">
                    <router-link v-if='isVueRoute' :to="{name: 'projects.overview', params: { project_id: project.id }}">
                        {{ project.name }}
                    </router-link>
                    <a v-else :href="`/projects/${project.id}/overview`">{{ project.name }}</a>
                </li>
                <li role="separator" class="dropdown-divider"></li>
                <li class="dropdown-item">
                    <router-link :to="{name: 'projects.create'}">
                        Create New Project
                    </router-link>
                </li>
            </ul>
        </li>

        <slot name='teams'></slot>

        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle"
                href="#" id="navbarDropdown"
                role="button"
                data-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false">
                {{ user.email }}
            </a>

            <ul class="dropdown-menu" role="menu">
                <li class="dropdown-item nav-item">
                    <a href="/logout"
                        onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();">
                        <i class="fa fa-btn fa-sign-out"></i>Logout
                    </a>
                    <form id="logout-form" action="/logout"
                        method="POST" style="display: none;">
                        <input type="hidden"
                            :value="$vfconfig.csfrToken()"
                            name="_token">
                    </form>
                </li>
                <li class="dropdown-item nav-item">
                    <router-link :to="{ name: 'my.account'}">
                        <i class="fa fa-btn fa-user"></i>My Account
                    </router-link>
                </li>

                <slot name='users'></slot>
            </ul>
        </li>
        </template>

        <template v-if='isGuest'>
        <li class="nav-item">
            <a class='nav-link' href="/login">Login</a>
        </li>
        <li class="nav-item">
            <a class='nav-link' href="/register">Register</a>
        </li>
        </template>

    </ul>
</div>
</template>

<script type="text/javascript">

import _ from 'lodash'
import { mapState } from 'vuex'

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
    props: {
        isVueRoute: {
            type: Boolean,
            default: true,
        }
    },

    //----------------------------------------------------------
    // Local State
    //-------------------------------------------------------
    data() {
        return {}
    },

    computed: {
        ...mapState(['projects', 'user']),
        isGuest() {
            return _.isEmpty(this.user)
        }
    },

    //----------------------------------------------------------
    // Events
    //-------------------------------------------------------
    // watch: {},
    mounted() {},
    // beforeDestroy() { /* dealloc anything you need to here*/ },

    //----------------------------------------------------------
    // Non-Reactive Properties
    //-------------------------------------------------------
    methods: {

    },
}
</script>

<style scoped lang="scss"></style>
