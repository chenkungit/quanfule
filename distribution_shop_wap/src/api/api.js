var url = "/v1";
var url2 = "/v2";
const api = {
  index: url2 + "/home/index",
  info: url + "/goods/info",
  price: url + "/goods/price",
  check_out: url2 + "/order/checkout",
  // 地址列表
  lists: url + "/address/lists",
  del_adr: url + "/address/delete",
  regions: url + "/address/regions",
  add: url + "/address/add",
  adr_info: url + "/address/info",
  adr_edit: url + "/address/edit",
  // 登录
  signin: url + "/auth/signin",
  signup: url + "/auth/signup",
  forget: url + "/auth/forget",
  // 获取验证码
  send: url2 + "/sms/send",
  done: url2 + "/pay/done",
  // 定案详情
  order_list: url + "/order/list",
  // 热搜接口
  hot: url + "/search/hot",
  prompt: url + "/search/prompt",
  // 分类列表
  parent_list: url + "/category/parent_list",
  product_list: url + "/category/product_list",
  // 搜索
  searchlist: url + "/search/sou",
  // 加入购物车
  addcart: url + "/cart/add",
  // 购物车列表
  cartlist: url + "/cart/lists",
  // 编辑购物车信息
  editcart: url + "/cart/edit",
  // 删除购物车商品
  delete: url + "/cart/delete",
  // 个人中心-个人信息
  userinfo: url + "/user/info",
  // 个人中心-修改密码
  modify_pwd: url2 + "/user/modify_password",
  // 立即购买
  editinfo: url + "/user/info_edit",
  // 我的订单
  orderlist: url + "/order/list",
  // 订单详情
  orderdetail: url + "/order/detail",
  //退货物流
  logistics_type: url + "/order/logistics_type",
  // 取消订单
  cancelorder: url + "/order/close",
  // 查看物流
  track: url + "/order/tracking",
  // 确认收货
  sure_receive: url + "/order/affirm_received",
  // 取消退货
  cancelcancel: url + "/order/cancel_refund",
  // 退货
  refund: url2 + "/order/refund",
  // 上传公共接口
  upload: url + "/upload",
  // 退出登录
  logout: url + "/auth/logout",
  captcha: url2 + "/sms/captcha",
  //评论列表
  comments: url + "/comment/lists",
  //二维码
  qrcodeshares: url + "/member/qrcode/share",

  qrcoderelate: url + "/member/qrcode/relate",
  //card
  cardLists: url + "/member/card",
  addCard: url + "/member/card",
  cardInfo: url + "/member/card/",
  cardUpdate: url + "/member/card/",
  cardDelete: url + "/member/card/",
  cardCheck: url + "/member/card/check",
  //提现
  withdraw: url + "/member/treasure/withdraw_apply",
  withdrawinfo: url + "/member/treasure/info",
  get_down_use: url + '/umbrella/relation/get_down_user',
  transfer: url + '/member/treasure/transfer',
  treasureconvert: url + '/member/treasure/convert',
  treasureflow: url + '/member/treasure/flow'
}
module.exports = api;
