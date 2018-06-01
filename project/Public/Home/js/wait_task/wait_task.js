var take = new Vue({
	el:"#app",
	data(){
		return{
			list:[],		//列表
			detail:"",		//服务详情
		}
	},
	methods:{
		openModal(){
			$('#my-alert').modal();  
		}
	}
});