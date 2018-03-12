import Scripts from './Scripts'
import ScriptForm from './ScriptForm'


export const routes = {
    path: 'scripts',
    name: 'projects.scripts',
    component: Scripts,
    children: [
             {
            path: ':id',
            name: 'projects.scripts.form',
            component: ScriptForm
        },
    ]
}

export {
    Scripts,
    ScriptForm
}