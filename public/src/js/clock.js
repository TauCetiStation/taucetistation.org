import Vue from 'vue';
import moment from 'moment';
import 'moment-timezone';

if(document.getElementById("header__clock")) {
	new Vue({
		el: '#header__clock',
		data: {
			updateInterval: null,
			time: "00:00",
		},
		methods: {
			update: function () {
				let ntime = moment();
				if(ntime.seconds()%2) {
					this.time = ntime.tz("Europe/Moscow").format('HH:mm');
				} else {
					this.time = ntime.tz("Europe/Moscow").format('HH mm');
				}
			},
		},
		mounted() {
			this.update();
			this.updateInterval = setInterval(function () {
				this.update();
			}.bind(this), 1000);
		},
		beforeDestroy() {
			if(this.updateInterval) {
				clearInterval(this.updateInterval);
			}
		},
		template: `<span class="header__clock">{{time}}</span>`,
	});
}
