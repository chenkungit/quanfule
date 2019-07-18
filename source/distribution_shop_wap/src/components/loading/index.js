const LoadingComponent = require('./Loading1.vue')

const loading = {
  install: function (Vue) {
    Vue.component('loading', LoadingComponent)
  }
}

// module.exports = loading
export default loading
