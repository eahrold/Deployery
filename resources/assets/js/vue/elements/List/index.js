import ListGroup from './ListGroup'
import ListGroupItem from './ListGroupItem'

export default {
    install(Vue, options) {
        Vue.component('list-group', ListGroup)
        Vue.component('list-group-item', ListGroupItem)
    }
}

export {
    ListGroup,
    ListGroupItem
}