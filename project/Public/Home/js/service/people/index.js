var peopleindex = new Vue({
	data() {
		return {
			task: {}
		}
	},
	created() {
		var vm = this;
		vm.task = {
			wait_install: 12,	// 待安装
			wait_repair: 16,	// 待维修
			wait_maintain: 20	// 待维护
		}
	},
	methods: {}
})