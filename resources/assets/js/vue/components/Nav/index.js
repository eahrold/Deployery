import { default as MainNav } from './MainNav'

export default {
    install(Vue, options) {
        Vue.component('main-nav', MainNav)
    }
}