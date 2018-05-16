import ProjectsList from './ProjectsList'
import Project from './Project'
import ProjectOverview from './ProjectOverview'
import ProjectDetails from './ProjectDetails'
import ProjectCreateModal from './ProjectCreateModal'


import { routes as ServerRoutes } from '../Servers'
import { routes as HistoryRoutes } from '../History'
import { routes as ConfigsRoutes } from '../Configs'
import { routes as ScriptsRoutes } from '../Scripts'

export const routes =  {
    path: '/',
    name: 'projects.list',
    component: ProjectsList,
    children: [
        {
            path: 'projects/create',
            name: 'projects.create',
            component: ProjectCreateModal,
        },
        {
            path: 'projects/:project_id',
            name: 'projects.edit',
            component: Project,

            children : [
                { path: 'overview', name: 'projects.overview', component: ProjectOverview},
                { path: 'details', name: 'projects.details', component: ProjectDetails},

                ServerRoutes,
                HistoryRoutes,
                ConfigsRoutes,
                ScriptsRoutes,

            ]
        }
    ]
}

export {
    ProjectsList,
    Project,
    ProjectOverview,
    ProjectDetails,
    ProjectCreateModal,
}