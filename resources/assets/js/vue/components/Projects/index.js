import ProjectsOverview from './ProjectsOverview'
import Project from './Project'
import ProjectInfo from './ProjectInfo'
import ProjectDetails from './ProjectDetails'
import ProjectForm from './ProjectForm'


import { routes as ServerRoutes } from '../Servers'
import { routes as HistoryRoutes } from '../History'
import { routes as ConfigsRoutes } from '../Configs'
import { routes as ScriptsRoutes } from '../Scripts'

export const routes =  {
    path: '/projects/:project_id',
    name: 'projects.edit',
    component: Project,

    children : [
        { path: 'info', name: 'projects.info', component: ProjectInfo},
        { path: 'details', name: 'projects.details', component: ProjectDetails},

        ServerRoutes,
        HistoryRoutes,
        ConfigsRoutes,
        ScriptsRoutes,

    ]
}

export {
    Project,
    ProjectsOverview,
    ProjectInfo,
    ProjectDetails,
    ProjectForm,
    ProjectChildMixin,
}