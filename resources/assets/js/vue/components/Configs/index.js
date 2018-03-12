import Configs from './Configs'
import ConfigForm from './ConfigForm'


export const routes = {
    path: 'configs',
    name: 'projects.configs',
    component: Configs,
    children: [
             {
            path: ':id',
            name: 'projects.configs.form',
            component: ConfigForm
        },
    ]
}

export {
    Configs,
    ConfigForm
}