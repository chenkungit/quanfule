import Vue from 'vue'
import Router from 'vue-router'

import Login from './views/Login.vue'
import NotFound from './views/404.vue'
import Home from './views/Home.vue'
import Main from './views/Main.vue'
//商品管理
import goods from './views/goodsall/goods.vue'
import goodsedit from './views/goodsall/goodscom/goodsedit.vue'
import goodsAdd from './views/goodsall/goodscom/goodsAdd.vue'
import goodsSpot from './views/goodsall/goodscom/goodsSpot.vue'
import goodsattr from './views/goodsall/goodsattr.vue'
import goodstype from './views/goodsall/goodstype.vue'
import category from './views/goodsall/category.vue'
import commentuser from './views/goodsall/commentuser.vue'
//订单管理
import order from './views/orderall/order.vue'
import back from './views/orderall/back.vue'
import backinfo from './views/orderall/back/backInfo.vue'
import orderInfo from './views/orderall/order/orderInfo.vue'

//页面管理
import carousel from './views/pageall/carousel.vue'
import hometheme from './views/pageall/hometheme.vue'
import nav from './views/pageall/nav.vue'
//管理员管理
import privilege from './views/privilegeall/privilege.vue'
// 会员管理
import setting from './views/vip/setting.vue'
import member from './views/vip/member.vue'
import relation from './views/vip/relation.vue'
// 财务管理
import withdraw from './views/treasure/withdraw'
import accountlog from './views/treasure/accountlog'
// 系统管理
import systemsetting from './views/system/setting'
Vue.use(Router)

var routes = [{
  path: '/login',
  component: Login,
  name: '',
  hidden: true
},
{
  path: '/404',
  component: NotFound,
  name: '',
  hidden: true
},
{
  path: '/',
  component: Home,
  // name: '首页',
  iconCls: 'fa fa-home',
  hidden: true,
  leaf: true, //图标样式class
  children: [{
    path: '/main',
    component: Main,
    name: '主页',
    hidden: false
  },
  {
    path: '/back/info/:id',
    component: backinfo,
    name: '退货单详情',
    hidden: true
  },
  {
    path: '/order/info/:id',
    component: orderInfo,
    name: '订单详情',
    hidden: true,
    meta: {
      keepAlive: false
    }
  },
  {
    path: '/goods/edit',
    component: goodsedit,
    name: '商品编辑',
    hidden: true
  },
  {
    path: '/goods/add',
    component: goodsAdd,
    name: '新增商品',
    hidden: true
  },
  {
    path: '/goods/spot/:id',
    component: goodsSpot,
    name: '现货管理',
    hidden: true
  }
  ],

},
]
var err = {
  path: '*',
  hidden: true,
  redirect: {
    path: '/404'
  }
}
var ass = {
  path: '/',
  component: Home,
  // name: '首页',
  iconCls: 'fa fa-wheelchair-alt',
  leaf: true, //图标样式class
  children: [
  ],

}
var allroutes = [{
  path: "/",
  component: Home,
  name: "商品管理",
  iconCls: "fa fa-shopping-cart",
  children: [{
    path: "/goods/lists",
    component: goods,
    name: "商品列表"
  },
  {
    path: "/goodstype/lists",
    component: goodstype,
    name: "商品类型"
  },
  {
    path: "/goodsattr/lists",
    component: goodsattr,
    name: "商品属性"
  },
  {
    path: "/category/lists",
    component: category,
    name: "商品分类"
  },
  // {
  //   path: "/commentuser/lists",
  //   component: commentuser,
  //   name: "用户评论"
  // }
  ]
},
{
  path: "/",
  component: Home,
  name: "订单管理",
  iconCls: "fa fa-book",
  children: [{
    path: "/order/lists",
    component: order,
    name: "订单列表",
    meta: {
      keepAlive: true
    }
  },
  {
    path: "/back/lists",
    component: back,
    name: "退货单列表",
    meta: {
      keepAlive: true
    }
  }
  ]
},
{
  path: "/",
  component: Home,
  name: "页面管理",
  iconCls: "fa fa-sticky-note",
  children: [{
    path: "/hometheme/lists",
    component: hometheme,
    name: "首页主题管理"
  },
  {
    path: "/carousel/lists",
    component: carousel,
    name: "广告管理"
  },
  {
    path: "/nav/lists",
    component: nav,
    name: "导航栏管理"
  }
  ]
},
{
  path: "/",
  component: Home,
  name: "会员管理",
  iconCls: "fa fa-ticket",
  children: [
    {
      path: "/member/lists",
      component: member,
      name: "会员列表"
    },
    {
      path: "/setting/lists",
      component: setting,
      name: "会员等级基本设置"
    }
    ,
    {
      path: "/umbrella/relation",
      component: relation,
      name: "会员伞下关系"
    }
  ]
},
{
  path: "/",
  component: Home,
  name: "财务管理",
  iconCls: "fa fa-money",
  children: [{
    path: "/withdraw/lists",
    component: withdraw,
    name: "提现申请列表",
    meta: {
      keepAlive: true
    }
  },
  {
    path: "/accountlog/lists",
    component: accountlog,
    name: "会员流水列表",
    meta: {
      keepAlive: true
    }
  }
  ]
},
{
  path: "/",
  component: Home,
  name: "系统管理",
  iconCls: "fa fa-cog",
  children: [{
    path: "/systemsetting/lists",
    component: systemsetting,
    name: "基本配置",
    meta: {
      keepAlive: true
    }
  }
  ]
},
{
  path: "/",
  component: Home,
  name: "权限管理",
  iconCls: "fa fa-user-o",
  children: [{
    path: "/privilege/lists",
    component: privilege,
    name: "管理员列表"
  }]
}
]
var router = new Router({
  routes: routes
})
router.beforeEach((to, from, next) => {
  var users = JSON.parse(sessionStorage.getItem('data'));

  if (to.path == '/login') {
    sessionStorage.removeItem('data');
  }
  if (!users && to.path != '/login') {
    next({
      path: '/login'
    })
  } else {
    next()
  }

  if (from.path == '/login') {
    var users = JSON.parse(sessionStorage.getItem('data'));
  }
  if (router.options.routes.length <= 4 && users) {
    for (var i in allroutes) {
      for (var j in users.data.menu) {
        if (users.data.menu[j].title == allroutes[i].name) {
          let sonarr = []
          for (var g in users.data.menu[j].child) {
            for (var h in allroutes[i].children) {
              if (users.data.menu[j].child[g].title == allroutes[i].children[h].name) {
                sonarr.push(allroutes[i].children[h])
              }
            }
          }
          allroutes[i].children = sonarr
          routes.push(allroutes[i])
        }
      }
    }
    routes.push(err)
    routes.push(ass)
    router.addRoutes(routes)
  }
})

export default router;
