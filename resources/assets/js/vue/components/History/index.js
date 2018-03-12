import History from './History'
import HistoryDetails from './HistoryDetails'


export const routes = {
    path: 'history',
    name: 'projects.history',
    component: History,
    children: [
             {
            path: ':id',
            name: 'projects.history.details',
            component: HistoryDetails
        },
    ]
}

export {
    History,
    HistoryDetails
}