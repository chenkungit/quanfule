import axios from 'axios';
import qs from 'qs'
import { Notification } from 'element-ui';
import router from "../router";
var instance = axios.create({
  headers: { 'content-type': 'application/json' }
});
instance.interceptors.request.use(//请求拦截
  config => {
    let a = JSON.parse(sessionStorage.getItem('data'));
    if (a) { // 判断是否存在token，如果存在的话，则每个http header都加上token
      config.headers = {
        accesstoken: a.data.accessToken
      }
    }
    return config;
  },
  err => {
    Notification.error({ title: '重新登录', message: '重新登录' });
    return Promise.reject(err);
  });
instance.interceptors.response.use(
  function (response) {
    return Promise.resolve(response)
  },
  function (error) {
    if (error.response.data.msg == "Expired token") {
      router.replace({
        path: '/login'
      })
      return Promise.reject(error)
    } else {
      Notification.error({ title: '网络请求错误', message: error.response.data.msg });
      return Promise.reject(error)
    }
  }
)
let base = '/dashboard';
let v1 = '/v1'
//登陆验证
export const requestLogin = params => { return instance.post(`${base}/auth/login`, params).then(res => res.data); };
//三级联动
export const regionList = params => { return instance.get(`${base}/order/getRegions`, { params: params }).then(res => res.data); };
//轮播图
export const carouselList = params => { return instance.get(`${base}/carousel/lists?limit=1000`, { params: params }).then(res => res.data); };
export const carouselAdd = params => { return instance.post(`${base}/carousel/add`, params).then(res => res.data); };
export const removecarousel = params => { return instance.delete(`${base}/carousel/delete`, { params: params }).then(res => res.data); };
export const editcarousel = params => { return instance.post(`${base}/carousel/edit`, params).then(res => res.data); };
//获取商品id
export const productslistid = params => { return instance.get(`${v1}/category/product_list/cat_id/` + params + '/limit/10000/beon') };
//nav
export const addNav = params => { return instance.post(`${base}/nav/add`, params).then(res => res.data); };
export const editNav = params => { return instance.post(`${base}/nav/edit`, params).then(res => res.data); };
export const navLists = params => { return instance.get(`${base}/nav/lists?limit=50`, { params: params }).then(res => res.data); };
export const removeNav = params => { return instance.delete(`${base}/nav/delete`, { params: params }).then(res => res.data); };

//主题
export const addhome = params => { return instance.post(`${base}/hometheme/add`, params); };
export const edithome = params => { return instance.post(`${base}/hometheme/edit`, params); };
export const homeLists = params => { return instance.get(`${base}/hometheme/lists?limit=1000`, { params: params }); };
export const removehomeLists = params => { return instance.delete(`${base}/hometheme/delete`, { params: params }); };
//分类
export const storecategoryLists = params => { return instance.get(`${base}/category/lists?type=1`, { params: params }); };
export const addcategory = params => { return instance.post(`${base}/category/add`, params); };
export const editcategory = params => { return instance.post(`${base}/category/edit`, params); };
export const categoryDelete = params => { return instance.delete(`${base}/category/delete`, { params: params }); };
//清除缓存
export const clearCache = params => { return instance.post(`${base}/clearCache`, params).then(res => res.data); };
export const clearCacheList = params => { return instance.post(`${base}/clearProducts`, params).then(res => res.data); };
//图片上传
export const uploadImg = params => { return instance.post(`${base}/ajax/upload`, params); };
//搜索接口
export const idSearch = params => { return instance.get(`${base}/ajax/search`, { params: params }); };
export const idSearchnew = params => { return instance.get(`${base}/ajax/search_new`, { params: params }).then(res => res.data); };
//管理员
export const privilegeLists = params => { return instance.get(`${base}/privilege/lists`, { params: params }); };
export const addPivilege = params => { return instance.post(`${base}/privilege/add`, params).then(res => res.data); };
export const allotPrivilege = params => { return instance.put(`${base}/privilege/allot`, params).then(res => res.data); };
export const allotPrivilegeLists = params => { return instance.get(`${base}/privilege/allot`, { params: params }); };
export const removePrivilege = params => { return instance.delete(`${base}/privilege/delete`, { params: params }); };
export const resetPwd = params => { return instance.put(`${base}/privilege/repassword`, params).then(res => res.data); };
export const forbidPrivilege = params => { return instance.post(`${base}/privilege/edit`, params).then(res => res.data); };
//快递单
export const deliveryLists = params => { return instance.get(`${base}/delivery/lists`, { params: params }); };
//退货单
export const backLists = params => { return instance.get(`${base}/refund/lists`, { params: params }); };
export const backInfo = params => { return instance.get(`${base}/refund/info`, { params: params }); };
export const backRefund = params => { return instance.post(`${base}/refund/back`, params).then(res => res.data); };
export const cancelBackrefund = params => { return instance.post(`${base}/refund/cancel_back`, params).then(res => res.data); };
// 商品类型
export const goodstypeLists = params => { return instance.get(`${base}/goodstype/lists`, { params: params }); };
export const addGoodstype = params => { return instance.post(`${base}/goodstype/add`, params).then(res => res.data); };
export const editGoodstype = params => { return instance.post(`${base}/goodstype/edit`, params).then(res => res.data); };
export const removeGoodstype = params => { return instance.delete(`${base}/goodstype/delete`, { params: params }); };

// 商品属性
export const seletedGoodsattr = params => { return instance.get(`${base}/goodsattr/selected`, { params: params }); };
export const seletedGoodsattrson = params => { return instance.get(`${base}/goodsattr/selected_list`, { params: params }); };
export const goodsattrLists = params => { return instance.get(`${base}/goodsattr/lists`, { params: params }); };
export const addGoodsattr = params => { return instance.post(`${base}/goodsattr/add`, params).then(res => res.data); };
export const editGoodsattr = params => { return instance.post(`${base}/goodsattr/edit`, params).then(res => res.data); };
export const removeGoodsattr = params => { return instance.delete(`${base}/goodsattr/delete`, { params: params }); };
//用户评论
export const commentuserLists = params => { return instance.get(`${base}/commentuser/lists`, { params: params }); };
export const commentuserInfo = params => { return instance.get(`${base}/commentuser/info`, { params: params }); };
export const editCommentuser = params => { return instance.post(`${base}/commentuser/edit`, params).then(res => res.data); };
export const commentuserSend = params => { return instance.post(`${base}/commentuser/send`, params).then(res => res.data); };
export const enableCommentuser = params => { return instance.put(`${base}/commentuser/enable`, params).then(res => res.data); };
// 商品列表
export const goodsLists = params => { return instance.get(`${base}/goods/lists`, { params: params }); };
export const goodseditInfo = params => { return instance.get(`${base}/goods/info`, { params: params }); };
export const addGoods = params => { return instance.post(`${base}/goods/add`, params).then(res => res.data); };
export const editGoods = params => { return instance.post(`${base}/goods/edit`, params).then(res => res.data); };
export const removeGoods = params => { return instance.delete(`${base}/goods/delete`, { params: params }); };
export const goodsUpdate = params => { return instance.post(`${base}/goods/update_sale`, params).then(res => res.data); };
export const goodsInsert = params => { return instance.post(`${base}/goods/sup_insert`, params).then(res => res.data); };
export const goodsSuppliers = params => { return instance.get(`${base}/goods/suppliers`, { params: params }); };
export const goodsAttrLists = params => { return instance.get(`${base}/goods/goods_attr`, { params: params }); };
export const spotAttrLists = params => { return instance.get(`${base}/goods/product_list`, { params: params }); };
export const goodsSpotLists = params => { return instance.get(`${base}/goods/products`, { params: params }); };
export const goodsSpotAdd = params => { return instance.post(`${base}/goods/products_add`, params); };
export const goodsSpotEdit = params => { return instance.post(`${base}/goods/products_edit`, params); };
export const goodsSpotDel = params => { return instance.delete(`${base}/goods/products_delete`, { params: params }); };
export const goodsEditLists = params => { return instance.get(`${base}/goods/sup_edit`, { params: params }); };
export const goodsDelRest = params => { return instance.get(`${base}/goods/sup_delete`, { params: params }); };
export const goodsSort = params => { return instance.get(`${base}/goods/order`, { params: params }); };
export const goodsCateList = params => { return instance.get(`${base}/goods/cat_tree_list`, { params: params }); };
//省市区
export const regionAddresss = params => { return instance.get(`${base}/ajax/region`, { params: params }); };
//订单
export const orderLists = params => { return instance.get(`${base}/order/lists`, { params: params }); };
export const orderlogistics_type = params => { return instance.get(`${base}/order/logistics_type`, { params: params }); };
export const asyncLogistics = params => { return instance.post(`${base}/order/asyncLogistics`, params).then(res => res.data); };
export const orderfahuo = params => { return instance.post(`${base}/order/fahuo`, params).then(res => res.data); };
export const orderInfo = params => { return instance.get(`${base}/order/info`, { params: params }); };
// 会员列表
export const memberLists = params => { return instance.get(`${base}/member/lists`, { params: params }); };
export const sendpoint = params => { return instance.post(`${base}/member/send_point`, params).then(res => res.data); };
export const consumeList = params => { return instance.get(`${base}/member/consume_list`, { params: params }).then(res => res.data); };
export const thankfulInfo = params => { return instance.get(`${base}/member/thankful_info`, { params: params }).then(res => res.data); };
export const thankfuledit = params => { return instance.post(`${base}/member/thankful_set`, params).then(res => res.data); };
//vip
export const vipLists = params => { return instance.get(`${base}/vip/setting`, { params: params }).then(res => res.data); };
export const vipadd = params => { return instance.post(`${base}/vip/setting`, params).then(res => res.data); };
export const vipedit = params => { return instance.put(`${base}/vip/setting/` + params.id, params).then(res => res.data); };
//伞下关系
export const topuserLists = params => { return instance.get(`${base}/umbrella/relation/top_user`, { params: params }).then(res => res.data); };
export const downuserLists = params => { return instance.get(`${base}/umbrella/relation/down_user`, { params: params }).then(res => res.data); };
//财务管理
//提现
export const withdrawLists = params => { return instance.get(`${base}/treasure/withdraw`, { params: params }).then(res => res.data); };
export const withdrawfinish = params => { return instance.post(`${base}/treasure/withdraw/finish`, params).then(res => res.data); };
//流水
export const accountlogLists = params => { return instance.get(`${base}/treasure/account_log`, { params: params }).then(res => res.data); };
//系统
export const systemLists = params => { return instance.get(`${base}/system/setting`, { params: params }).then(res => res.data); };
export const systemput = params => { return instance.put(`${base}/system/setting/`+params.id, params).then(res => res.data); };