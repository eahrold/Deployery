import LoadingIndicator from './LoadingIndicator'
import TrashButton from './TrashButton'
import FormCard from './FormCard'
import FormSection from './FormSection'

import ListPlugin, { ListGroup, ListGroupItem } from './List'

export default {
    install(Vue, options) {
        Vue.component('loading-indicator', LoadingIndicator)
        Vue.component('trash-button', TrashButton)
        Vue.component('form-card', FormCard)
        Vue.component('form-section', FormSection)

        Vue.use(ListPlugin)
    }
}

export {
    LoadingIndicator,
    TrashButton,
    FormCard,
    FormSection,
    ListGroup,
    ListGroupItem
}