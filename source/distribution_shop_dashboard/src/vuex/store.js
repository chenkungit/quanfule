import Vue from 'vue'
import Vuex from 'vuex'
import * as actions from './actions'
import * as getters from './getters'
import { getProducts } from '../api/api'
import { getProductslist } from '../api/api'



Vue.use(Vuex)

// 应用初始状态
export const state = {
    count: 10,
    logining: false,
    products: [],
    token: { accessToken: '' },
    vx_order_info: ''
}

// 定义所需的 mutations
const mutations = {
    SETTOKEN(state) {
        if (JSON.parse(sessionStorage.getItem('data'))) {
            let a = JSON.parse(sessionStorage.getItem('data'));
            state.token.accessToken = a.data.accessToken
        }
    },
    QUERYORDERINFO(state, res) {
        state.vx_order_info = res.data
    },
    INCREMENT(state) {
        state.count++
    },
    DECREMENT(state) {
        state.count--
    }
}

// 创建 store 实例
export default new Vuex.Store({
    actions,
    getters,
    state,
    mutations
})