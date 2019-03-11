import _ from 'lodash'
import { Api } from '../services'

import types from './types'


const DeploymentSchema=()=>{
    return {
        messages: [],
        progress: 0,
        stage: -1,
        server_id: null,
        server_name: null,
        deploying: false,
        errors: [],
    }
}

const ProjectInfoSchema=()=>{
    return {
        deployments: {},
        repo: {},
        status: {},
    }
}


export default {

    state: {
        user: {},
        project: {},
        proejctsLoading: false,
        projects: [],
        info: ProjectInfoSchema(),
        deployment: DeploymentSchema(),
        history: [],
        viewers: [],
        actionTypes: types,
    },

    mutations: {
        user (state, payload) {
            state.user = payload.user
        },

        project (state, payload) {
            state.project = payload.project
        },

        projects (state, payload) {
            state.projects = payload.projects
        },

        info (state, payload) {
            state.info = payload.info
        },

        proejctsLoading (state, payload) {
            state.proejctsLoading = payload.proejctsLoading
        },

        deployment (state, payload) {
            state.deployment = payload.deployment
        },

        viewers (state, payload) {
            state.viewers = payload.viewers
        },

        history (state, payload) {
            state.history = payload.history
        }
    },

    getters: {
        hasUser: (state, getters) => {
            return !_.isEmpty(state.user)
        },

        hasProject: (state, getters) => {
            return !_.isEmpty(state.project)
        },

        messages: (state, getters) => {
            return _.get(state, 'deployment.messages', [])
        },

        deploying: (state, getters) => {
            return _.get(state, 'deployment.deploying', false)
        },

        progress: (state, getters) => {
            return _.get(state, 'deployment.progress', 0)
        },

        stage: (state, getters) => {
            return _.get(state, 'deployment.stage', -1)
        },

        lastDeployment: (state, getters) => {
            return _.first(state.history)
        },

        /**
         * Return the id of the server that is deploying
         *
         * @return number   id of the server
         */
        deployingServerId: (state, getters)=>{
            return _.get(state.deployment, 'server_id');
        },

        /**
         * Return the id of the server that is deploying
         *
         * @return string   name of the server
         */
        deployingServerName: (state, getters)=>{
            return _.get(state.deployment, 'server_name');
        },

        /**
         * General overview message about deployment status
         *
         * @return string  message about deployment status
         */
        deployingStatusMessage:(state, getters)=>{
            return (getters.deployingServerName || 'This Project') + ' is currently deploying.'
        },

        /*
         * Last sent deployment message.
         */
        deployingCurrentMessage: (state, getters)=>{
            if(getters.deploying){
                return _.first(getters.messages);
            }
        }
    },

    actions: {
        [types.HISTORY_SET]({commit, state}, { history }){
            commit('history', { history })
        },

        [types.HISTORY_APPEND]({commit, state}, {entry}){
            const { history } = state
            history.unshift(entry)
            commit('history', { history })
        },

        /**
         * Handle the DeploymentStarted event message
         *
         * @param  object data event data
         */
        [types.DEPLOYMENT_RESET]({commit, state}){
            const deployment = {
                messages: [],
                progress: 0,
                stage: -1,
                server_id: null,
                server_name: null,
                deploying: false,
            }
            commit('deployment', { deployment })
        },

        /**
         * Handle the DeploymentStarted event message
         *
         * @param  object data event data
         */
        [types.DEPLOYMENT_STARTED]({commit, state}, {data}){
            const { message, server } = data
            const deployment = {
                messages: [ message ],
                progress: 0,
                server_id: server.id,
                server_name: server.name,
                stage: 0,
                deploying: true,
            }
            commit('deployment', { deployment })
        },

        /**
         * Handle the DeploymentMessage event message
         *
         * @param  object data event data
         */
        [types.DEPLOYMENT_PROGRESS]({commit, state}, {data}){
            // console.log("Progress", {data})
            const { errors, progress, message, server_id, server_name, stage } = data
            const { deployment } = state

            if (server_id) deployment.server_id = server_id
            if (server_name) deployment.server_name = server_name
            if (progress) deployment.progress = progress
            if (stage) deployment.stage = stage
            if (message) deployment.messages.unshift(message)
            if (!_.isEmpty(errors)) deployment.errors = _.merge(deployment.errors, errors)

            commit('deployment', { deployment })
        },

        /**
         * Handle the DeploymentStarted event message
         *
         * @param  object data event data
         */
        [types.DEPLOYMENT_ENDED]({commit, state}, {data}){
            const { message } = data
            const { messages } = state.deployment

            console.log("Deployment Ended", {data,})

            const deployment = {
                ...state.deployment,
                messages: _.merge(messages, [message]),
                progress: 100,
                server_id: null,
                server_name: null,
                deploying: false,
                stage: 100,
            }
            commit('deployment', { deployment })
        },

        [types.VIEWERS_SET]({commit, state}, { viewers }){
            commit('viewers', { viewers })
        },

        [types.USER_SET] ({commit}, { user }) {
            commit('user', { user })
        },

        [types.PROJECTS_LOAD]({commit, state}) {
            commit('proejctsLoading', {proejctsLoading: true, })
            Api.projects().then(({data, pagination})=>{
                commit('projects', { projects: data })
                commit('proejctsLoading', {proejctsLoading: false, })
                return { data, pagination }
            }).catch((error)=>{
                commit('proejctsLoading', {proejctsLoading: false, })
                throw error
            })
        },

        [types.PROJECT_SET] ({commit, state}, { project }) {
            commit('project', { project })
        },

        [types.PROJECT_UPDATE] ({commit, state}, { project }) {
            return Api.projectSave(project).then((response)=>{
                const idx = _.findIndex(state.projects, {id: project.id})
                if(idx !== -1) {
                    let projects = state.projects
                    projects[idx] = _.assign({}, projects[idx], project)
                    commit('projects', { projects })
                }
                return response
            })
        },

        [types.PROJECT_DELETE] ({commit, state}, { project }) {
            return Api.projectDelete(project).then((response)=>{
                const projects = _.filter(state.projects, (aProject)=>{
                    return aProject.id !== project.id
                })
                commit('projects', { projects })
            })
        },

        [types.PROJECT_RESET] ({commit, state}) {
            commit('project', { project: {} })
            commit('history', { history: [] })
            commit('deployment', { deployment: DeploymentSchema() })
        },

        [types.PROJECT_INFO_SET] ({commit, state}, { info }) {
            commit('info', { info: info })
        },

        [types.PROJECT_INFO_RESET] ({commit, state}) {
            commit('info', { info: {} })
        },

    }
}
