import LoadingIndicator from './LoadingIndicator'
import TrashButton from './TrashButton'
import FormCard from './FormCard'
import FormSection from './FormSection'
import PubKey from './PubKey'

import ListPlugin, { ListGroup, ListGroupItem } from './List'
import ProgressPlugin from './Progress'

export default {
    install(Vue, options) {
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
    TrashButton,
    FormCard,
    FormSection,
    ListGroup,
    ListGroupItem,
    PubKey
}