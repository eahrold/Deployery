import LoadingIndicator from './LoadingIndicator'
import TrashButton from './TrashButton'

export default {
    install(Vue, options) {
        Vue.component('loading-indicator', LoadingIndicator)
        Vue.component('trash-button', TrashButton)
    }
}

export {
    LoadingIndicator,
    TrashButton
}