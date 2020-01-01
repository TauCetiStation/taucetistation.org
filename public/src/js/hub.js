import Vue from 'vue';
import axios from 'axios';
import moment from 'moment';
import 'moment-duration-format';

if(typeof(hubServers) !== 'undefined' && document.getElementById("hub")) {

	const GS_STARTUP = 0,
	GS_PREGAME = 1,
	GS_SETTING_UP = 2,
	GS_PLAYING = 3,
	GS_FINISHED = 4;

	Vue.component('hub__server', {
		props: ['server'],
		data: function () {
			return {
				updateInterval: null,
				loading: true,
				name: null,
				roundduration: null,
				stationtime: null,
				players: null,
				popcap: null,
				mode: null,
				error: false,
				gamestate: GS_PLAYING,
				cached: false,
			};
		},
		computed: {
			restart: function() {
				return this.gamestate==GS_FINISHED;
			},
			roundstart: function() {
				return this.gamestate<=GS_PREGAME;
			},
		},
		methods: {
			update: function() {
				axios.get('/server/' + this.server.shortname + '/json')
					.then(response => {
						this.stationtime = response.data.stationtime;
						this.players = response.data.players;

						if(typeof response.data.roundduration !== 'undefined') {//roundduration - formatted Bay style
							this.roundduration = response.data.roundduration;
						} else if (typeof response.data.round_duration !== 'undefined') {//round_duration - not formatted /tg/ stile
							this.roundduration = moment.duration(response.data.round_duration, "seconds").format("*HH:mm");
						}

						this.popcap = response.data.popcap;
						this.mode = response.data.mode;

						if(typeof response.data.gamestate !== 'undefined') {
							this.gamestate = response.data.gamestate;
						}

						this.cached = response.data.cached;
						this.error = response.data.error;
					})
					.catch(error => {
						console.log(error);
						this.error = true;
					})
					.then(() => (this.loading = false));
			},
			stateClass: function() {
				return "error";
			}
		},
		mounted() {
			this.update();
			this.updateInterval = setInterval(function () {
				this.update();
			}.bind(this), 30000);
		},
		beforeDestroy() {
			if(this.updateInterval) {
				clearInterval(this.updateInterval);
			}
		},
		template://todo: click.alt update?
			`<a :href="server.address" class="hub__server" :class="{'hub__server_error': error, 'hub__server_restart': restart, 'hub__server_roundstart': roundstart, 'hub__server_side': server.hubSidetag}">
				<ul>
					<li>{{server.servername}}</li>

					<li v-if='loading || error'>Duration: --:--</li>
					<li v-else-if='roundduration'>Duration: {{roundduration}}</li>
					<li v-else>Station time: {{stationtime}}</li>

					<li v-if='loading || error'>Crew: --</li>
					<li v-else-if='popcap'>Crew: {{players}}/{{popcap}}</li>
					<li v-else>Crew: {{players}}</li>

					<li v-if='loading'>Loading...</li>
					<li v-else-if='error'>Error</li>
					<li v-else-if='restart'>Restart</li>
					<li v-else-if='roundstart'>Round Start</li>
					<li v-else>{{mode}}</li>

				</ul>
			</a>`,
	});


	/*todo: api, сбор с настроек
	if(typeof(hubServers) !== 'undefined');*/

	new Vue({
		el: '#hub',
		data: {
			servers: hubServers,
			expanded: false,
		},
		methods: {
			toggleExpand: function () {
				this.expanded = !this.expanded;
			},
		},
		computed: {
			hasHiddenServers: function() {
				return !!this.servers.filter(server => !(server.hubShowAlways || server.hubShowDefault)).length;
			},
		},
	});
};
