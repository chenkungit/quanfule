import index from './page/index/index.vue'
import GoodsDetail from './page/goodsDetail/GoodsDetail.vue'
import Editorder from './page/editOrder/editOrder.vue'
import adr_list from './page/adr_list/adr_list.vue'
import new_adr from './page/new_adr/new_adr.vue'
import login from './page/login/login.vue'
import register from './page/register/register.vue'
import serve_txt from './page/register/serve_txt.vue'
import reset_pwd from './page/reset_pwd/reset_pwd.vue'
import search from './page/search/search.vue'
import sort from './page/sort/sort.vue'
import category from './page/category/category.vue'
import searchlist from './page/search/searchlist.vue'
import goodslist from './page/goodsDetail/goodslist.vue'
import shopcart from './page/shopcart/shopcart.vue'
import usercenter from './page/usercenter/usercenter.vue'
import amount from './page/usercenter/amount.vue'
import myinfo from './page/usercenter/myinfo.vue'
import userorder from './page/userorder/userorder.vue'
import orderdetail from './page/userorder/orderdetail.vue'
import track from './page/userorder/track.vue'
import afterservice from './page/userorder/afterservice.vue'
import forget_pwd from './page/reset_pwd/forget_pwd.vue'
import jundgeList from './page/goodsDetail/jundgeList.vue'
import myshare from './page/usercenter/myshare.vue'
import choosevip from './page/usercenter/choosevip.vue'
import mycard from './page/usercenter/card/mycard.vue'
import cardadd from './page/usercenter/card/cardadd.vue'
import cardedit from './page/usercenter/card/cardedit.vue'
import withdraw from './page/usercenter/withdraw.vue'
import withdrawlist from './page/usercenter/withdrawlist.vue'
import mycurrent from './page/usercenter/mycurrent.vue'
import allocate from './page/usercenter/allocate/allocatepage.vue'
import myumbrella from './page/usercenter/allocate/myumbrella.vue'
import allocatelist from './page/usercenter/allocate/allocatelist.vue'
import not_found_404 from './page/not_found_404/not_found_404.vue'




export default [
    { path: '/mycurrent', component: mycurrent, name: 'mycurrent' },
    { path: '/allocatelist', component: allocatelist, name: 'allocatelist' },
    { path: '/allocate', component: allocate, name: 'allocate' },
    { path: '/myumbrella', component: myumbrella, name: 'myumbrella' },
    { path: '/withdrawlist', component: withdrawlist, name: 'withdrawlist' },
    { path: '/withdraw', component: withdraw, name: 'withdraw' },
    { path: '/cardedit', component: cardedit, name: 'cardedit' },
    { path: '/cardadd', component: cardadd, name: 'cardadd' },
    { path: '/mycard', component: mycard, name: 'mycard' },
    { path: '/myshare', component: myshare, name: 'myshare' },
    { path: '/choosevip', component: choosevip, name: 'choosevip' },
    { path: '/index', component: index, name: 'index' },
    { path: '/goodsDetail', component: GoodsDetail, name: 'goodsDetail' },
    { path: '/editOrder', component: Editorder, name: 'editOrder' },
    { path: '/adr_list', component: adr_list, name: 'adr_list' },
    { path: '/new_adr', component: new_adr, name: 'new_adr' },
    { path: '/login', component: login, name: 'login' },
    { path: '/register', component: register, name: 'register' },
    { path: '/serve_txt', component: serve_txt, name: 'serve_txt' },
    { path: '/reset_pwd', component: reset_pwd, name: 'reset_pwd' },
    { path: '/search', component: search, name: 'search' },
    { path: '/sort', component: sort, name: 'sort' },
    { path: '/category', component: category, name: 'category' },
    { path: '/searchlist/:keywords', component: searchlist, name: 'searchlist' },
    { path: '/goodslist', component: goodslist, name: 'goodslist' },
    { path: '/shopcart', component: shopcart, name: 'shopcart' },
    { path: '/usercenter', component: usercenter, name: 'usercenter' },
    { path: '/amount', component: amount, name: 'amount' },
    { path: '/myinfo', component: myinfo, name: 'myinfo' },
    { path: '/userorder/:status', component: userorder, name: 'userorder' },
    { path: '/orderdetail/:id', component: orderdetail, name: 'orderdetail' },
    { path: '/track/:id', component: track, name: 'track' },
    { path: '/afterservice/:orderid', component: afterservice, name: 'afterservice' },
    { path: '/forget_pwd', component: forget_pwd, name: 'forget_pwd' },
    { path: '/jundgeList', component: jundgeList, name: 'jundgeList' },



    { path: '/not_found_404', component: not_found_404, name: 'not_found_404' },


    { path: '*', redirect: '/index' }

]
