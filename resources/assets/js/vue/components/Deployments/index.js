import Deployment from './Deployment'
import Deployments from './Deployments'
import DeploymentInfoPanel from './DeploymentInfoPanel'

export default {
    install(Vue, options) {
        Vue.component('deployment', Deployment);
        Vue.component('deployments', Deployments);
        Vue.component('deployments-info-panel', DeploymentInfoPanel);
    }
}
export {
    Deployment,
    Deployments
}