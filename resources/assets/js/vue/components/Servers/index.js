import Servers from './Servers'
import ServerForm from './ServerForm'

import { Deployment } from '../Deployments'

export const routes = {
    path: 'servers',
    name: 'projects.servers',
    component: Servers,
    children: [
        {
            path: ':id',
            name: 'projects.servers.form',
            component: ServerForm
        },
        {
            path: ':id/deploy',
            name: 'projects.servers.deploy',
            component: Deployment
        }
    ]
}

export {
    Servers,
    ServerForm
}