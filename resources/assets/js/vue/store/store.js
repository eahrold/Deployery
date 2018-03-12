import _ from 'lodash'

import types from './types'

export default {

    state: {
        user: {},
        project: {},
        deployment: {},
        actionTypes: types,
    },

    mutations: {
        user (state, payload) {
            state.user = payload.user
        },

        project (state, payload) {
            state.project = payload.project
        },

        deployment (state, payload) {
            state.deployment = payload.deployment
        },
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
        /**
         * Handle the DeploymentStarted event message
         *
         * @param  object data event data
         */
        [types.DEPLOYMENT_RESET]({commit, state}){
            const deployment = {
                messages: [],
                progress: 0,
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
            const { errors, progress, message } = data
            const { deployment } = state

            if (errors) deployment.errors = errors
            if (progress) deployment.progress = progress
            if (message) deployment.messages.unshift(message)

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

            const deployment = {
                messages: _.merge(messages, [message]),
                progress: 100,
                server_id: null,
                server_name: null,
                deploying: false,
            }
            commit('deployment', { deployment })
        },


        [types.USER_SET] ({commit}, { user }) {
            commit('user', { user })
        },

        [types.PROJECT_SET] ({commit, state}, { project }) {
            commit('project', { project })
        },

        [types.MESSAGES_SET] ({commit, state, getters}, { messages }) {
            commit('project', { messages })
        },

        [types.MESSAGES_APPEND] ({commit, state, getters}, { message }) {
            const messages =  state.messages
            messages.unshift(message);
            commit('messages', { messages })
        },
    }
}
