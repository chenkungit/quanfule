// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import FastClick from 'fastclick'
import VueClipboard from 'vue-clipboard2'
import VueRouter from 'vue-router'
import RouterConfig from './routerConfig.js'
import App from './App'
import { AjaxPlugin, ToastPlugin } from 'vux'
import animate from "animate.css"
import loading from './components/loading/'
import stores from './store/index.js'
import VueLazyLoad from 'vue-lazyload'
import Vant from 'vant';
import 'vant/lib/index.css';
import { Uploader } from 'vant';
import './assets/js/globals.js';
import $ from 'jquery';
import { Dialog } from 'vant';



Vue.use(Uploader);


Vue.use(loading)
Vue.use(animate)
Vue.use(VueClipboard)
Vue.use(Vant);



Vue.use(VueLazyLoad, {
  error: './static/error1.png',
  loading: './static/loading2.gif'
})

AjaxPlugin.$http.interceptors.request.use(
  config => {
    stores.dispatch('showloading')

    var com = config.headers.common;
    if (localStorage.getItem("key")) {
      com.key = localStorage.getItem("key");
      com.source = 'wap_h5';
    }
    return config;
  },
  
  err => {
    return Promise.reject(err)
  }
)

AjaxPlugin.$http.interceptors.response.use(
  response => {
    stores.dispatch('hideloading')
    if (response.data.code == 2001 || response.data.code == 2004) {
      router.push({
        name: "login"
      })
    }
    return response;
  },
  error => {

    if (error.response) {
      switch (error.response.status) {
        case 500:
          //        router.replace({name:"login"})
          break;

      }
    }
    // console.log(error)
    // console.log(error.response)
    return Promise.reject(error.response.data)
  }

)

Vue.use(AjaxPlugin)
Vue.use(ToastPlugin)


// import "./assets/js/ntkfstat.js";


require('./assets/css/reset_css.css')
Vue.use(VueRouter)

const router = new VueRouter({ routes: RouterConfig ,base:'/mobile/',mode:'history'})
router.afterEach((to, from, next) => {
  window.scrollTo(0, 0);
});
FastClick.attach(document.body)

Vue.config.productionTip = false

/* eslint-disable no-new */
new Vue({
  store: stores,
  router,
  render: h => h(App)

}).$mount('#app-box')
