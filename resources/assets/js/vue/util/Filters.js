import _ from 'lodash'


export default function(Vue, options) {
    Vue.filter('capitalize', _.capitalize)
    Vue.filter('kebabCase', _.kebabCase)
    Vue.filter('lowerCase', _.lowerCase)
    Vue.filter('lowerFirst', _.lowerFirst)
    Vue.filter('snakeCase', _.snakeCase)
    Vue.filter('startCase', _.startCase)
    Vue.filter('toUpper', _.toUpper)
    Vue.filter('toLower', _.toLower)
    Vue.filter('trim', _.trim)
    Vue.filter('trimStart', _.trimStart)
    Vue.filter('trimEnd', _.trimEnd)
    Vue.filter('upperCase', _.upperCase)
    Vue.filter('upperFirst', _.upperFirst)

    Vue.filter('truncate', function (value, options) {
        if(_.isNumber(options)) {
            return _.truncate(value, {length: options})
        } else {
            return _.truncate(value, options)
        }
    })

    Vue.filter('unsnake', function(value){
        return _.filter(`${value}`.split('_'), _.identity).join(' ')
    })
}