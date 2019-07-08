import getters from './getters'
const state = {
	
	loading:false
}

const mutations = {
	
	showloading(state){
		state.loading=true;
	},
	hideloading(state){
		state.loading=false;
	}


}

export default {
	state,   
	mutations,
	getters
}
