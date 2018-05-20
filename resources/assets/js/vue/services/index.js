import Api from './Api'

const ApiPlugin = {
    install(Vue, option) {
        Object.defineProperty(Vue.prototype, '$api', {
            get () {
                return Api
            }
        })
    }
}

export {  Api, ApiPlugin }

