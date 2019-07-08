// import babelpolyfill from 'babel-polyfill'
import Vue from 'vue'
import App from './App'
import ElementUI from 'element-ui'
import 'element-ui/lib/theme-chalk/index.css'
import Vuex from 'vuex'
import store from './vuex/store'
import 'font-awesome/css/font-awesome.min.css'

import VueImgInputer from 'vue-img-inputer'
import router from './router'
import until from './until'
import VueHtml5Editor from 'vue-html5-editor'
import 'video.js/dist/video-js.css'
import echarts from 'echarts'
Vue.use(VueHtml5Editor,{
    language: "zh-cn",
    // 自定义语言
    i18n: {
        //specify your language here
        "zh-cn": {
            "align": "对齐方式",
            "image": "图片",
            "list": "列表",
            "link": "链接",
            "unlink": "去除链接",
            "table": "表格",
            "font": "文字",
            "full screen": "全屏",
            "text": "排版",
            "eraser": "格式清除",
            "info": "关于",
            "color": "颜色",
            "please enter a url": "请输入地址",
            "create link": "创建链接",
            "bold": "加粗",
            "italic": "倾斜",
            "underline": "下划线",
            "strike through": "删除线",
            "subscript": "上标",
            "superscript": "下标",
            "heading": "标题",
            "font name": "字体",
            "font size": "文字大小",
            "left justify": "左对齐",
            "center justify": "居中",
            "right justify": "右对齐",
            "ordered list": "有序列表",
            "unordered list": "无序列表",
            "fore color": "前景色",
            "background color": "背景色",
            "row count": "行数",
            "column count": "列数",
            "save": "确定",
            "upload": "上传",
            "progress": "进度",
            "unknown": "未知",
            "please wait": "请稍等",
            "error": "错误",
            "abort": "中断",
            "reset": "重置",
            'insertHTML': 'insertHTML',
        }
    },
    // 配置图片模块
    // config image module
    image: {
      // 文件最大体积，单位字节  max file size
      sizeLimit: 51200 * 102400,
      // 上传参数,默认把图片转为base64而不上传
      // upload config,default null and convert image to base64
      upload: {
        url: '/dashboard/ajax/upload',
        headers: {},
        params: {},
        fieldName: 'img'
      },
      // 压缩参数,默认使用localResizeIMG进行压缩,设置为null禁止压缩
      // compression config,default resize image by localResizeIMG (https://github.com/think2011/localResizeIMG)
      // set null to disable compression
      compress: {
        width: 1600,
        height: 1600,
        quality: 80
      },
      // 响应数据处理,最终返回图片链接
      // handle response data，return image url
      uploadHandler(responseText){
        //default accept json data like  {ok:false,msg:"unexpected"} or {ok:true,data:"image url"}
        var json = JSON.parse(responseText);
        if (json.code == 200) {
          return json.data.img_url;
        } else {
          alert(json.msg)
        }
      }
    },
});
Vue.use(until);
Vue.use(ElementUI,{ size: 'mini' })
Vue.use(Vuex)
Vue.component('VueImgInputer', VueImgInputer)
Vue.prototype.$echarts = echarts
new Vue({
    //el: '#app',
    //template: '<App/>',
    router,
    store,
    //components: { App }
    render: h => h(App)
}).$mount('#app')
