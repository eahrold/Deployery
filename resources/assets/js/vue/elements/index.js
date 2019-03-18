import LoadingIndicator from './LoadingIndicator'
import HidingLoader from './HidingLoader'
import TrashButton from './TrashButton'
import FormCard from './FormCard'
import FormSection from './FormSection'
import PubKey from './PubKey'

import ListPlugin, { ListGroup, ListGroupItem } from './List'
import ProgressPlugin from './Progress'

export default {
    install(Vue, options) {
        Vue.component('hiding-loader', HidingLoader)
        Vue.component('loading-indicator', LoadingIndicator)
        Vue.component('trash-button', TrashButton)
        Vue.component('form-card', FormCard)
        Vue.component('form-section', FormSection)
        Vue.component('pub-key', PubKey)

        Vue.use(ListPlugin)
        Vue.use(ProgressPlugin)
    }
}

export {
    LoadingIndicator,
    HidingLoader,
    TrashButton,
    FormCard,
    FormSection,
    ListGroup,
    ListGroupItem,
    PubKey
}