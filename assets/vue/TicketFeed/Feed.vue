<template>
    <div class="d-flex align-items-center mb-4">
        <div class="btn-group" role="group" aria-label="Basic checkbox toggle button group">
            <input @click="toggleNewTickets" type="checkbox" class="btn-check" id="new" autocomplete="off">
            <label class="btn btn-primary" for="new">New Tickets</label>

            <input type="checkbox" class="btn-check" id="admins">
            <label @click="toggleNoAdmins" class="btn btn-primary" for="admins">Servers with No Admins</label>

            <input type="checkbox" class="btn-check" id="mute">
            <label @click="mute" class="btn btn-success" for="mute"><i class="fa fa-fw"
                    :class="[muted ? 'fa-volume-mute' : 'fa-volume-up']"></i>
                {{ muted ? "Unmute Sound" : "Mute Sound" }}</label>
            <button type="button" @click="purgeTickets" class="btn btn-danger">Clear Tickets</button>
        </div>
        <p class="ms-4 my-0 text-center">{{ messages.text }}</p>
    </div>
    <div class="grid" style="--bs-gap: 1rem;">
        <div v-for="(s, server) in servers" v-bind:key="s.identifier" @click="toggleServer(server, s.identifier)"
            class="g-col-4 d-flex justify-content-between align-items-center m-0 alert p-1 server-banner" :class="{
                'alert-info': undefined === s.gamestate,
                'alert-success': 0 === s.gamestate,
                'alert-success': 1 === s.gamestate,
                'alert-info': 2 === s.gamestate,
                'alert-info': 3 === s.gamestate,
                'alert-danger': 4 === s.gamestate,
                'alert-danger': s.error,
                'opacity-50': !s.toggled,
                'delta-mode alert-danger': 'delta' == s.security_level,
            }"><span>{{ s.identifier }}
                <span v-if="!s.error">(<i class="fas fa-user" title="Players"></i> {{ s.players }},
                    <i class="fas fa-user-shield" title="Admins"></i>
                    {{ s.admins }})
                    {{ secondsToTime(s.round_duration) }}</span><br />
                <span v-if="'idle' != s.shuttle_mode && s.shuttle_mode" style="font-size: .75rem;"><i class="fas fa-rocket"
                        title="Shuttle Status"></i>
                    {{ secondsToTime(s.shuttle_timer) }} -
                    <span class="capitalize">{{ s.shuttle_mode }}</span>
                </span>
            </span>
            <span>
                <i v-if="s.hub" class="fas fa-globe pe-2" tite="Server is On The Hub"></i>
                <span v-if="s.security_level">
                    <i class="fas fa-circle" title="Security Level" :class="{
                        'text-danger': 'red' === s.security_level,
                        'text-info': 'blue' === s.security_level,
                        'text-success': 'green' === s.security_level,
                        'text-yellow-400 animate-ping': 'delta' === s.security_level,
                    }"></i>
                </span>
            </span>
        </div>
    </div>
    <dl class="list-group list-group-flush border-top mt-4">
        <div v-if="!this.tickets.length" class="list-group-item text-center">
            « <i class="fas fa-spinner fa-pulse"></i> Loading Tickets... »
        </div>
        <ticketEntry v-else v-for="t in tickets" :key="t.id" :id="t.id" :class="{ hidden: t.hide, added: t.isNew }" class="ticket"
            :t="t">
        </ticketEntry>
    </dl>
</template>
  
<style>
.capitalize {
    text-transform: capitalize;
}
</style>

<script>

const initialTicketUrl = "?json=true";
const pollUrl = "?json=true";
const serverUrl = "https://tgstation13.org/dynamicimages/serverinfo.json";
const SQLFormat = 'y-MM-dd TT'

import ticketEntry from "./ticketEntry.vue";

import { DateTime } from "luxon";

export default {
    components: {
        ticketEntry,
    },
    data() {
        return {
            tickets: [],
            muted: true,
            messages: {
                type: "info",
                text: "Checking for new tickets...",
            },
            newTickets: false,
            noAdmins: false,
            servers: [],
            toggledServers: [],
            lastTicket: null
        };
    },
    methods: {
        secondsToTime(seconds) {
            var output = '';
            if (seconds >= 86400)
                output += Math.floor(seconds / 86400) + ':';
            if (seconds >= 3600)
                output += this.pad(Math.floor(seconds / 3600) % 24, 2) + ':';
            output += this.pad(Math.floor((seconds / 60) % 60), 2) + ':' + this.pad(Math.floor(seconds) % 60, 2);
            return output;
        },
        pad(n, width, z) {
            z = z || '0';
            n = n + '';
            return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
        },
        fetchServerList() {
            fetch(serverUrl)
                .then((res) => res.json())
                .then((res) => {
                    this.servers = []
                    delete res.refreshtime;
                    Object.entries(res).forEach((server) => {
                        server = server[1]
                        server.toggled = true
                        if (this.noAdmins) {
                            if (server.admins > 0 && server.admins) {
                                server.toggled = false
                            } else {
                                server.toggled = true
                            }
                        }
                        if (false == this.toggledServers[server.identifier]) {
                            server.toggled = false;
                        }
                        if (server.version && "/tg/Station 13" == server.version) {
                            this.toggledServers[server.identifier] = server.toggled
                            this.servers.push(server)
                        }
                    })
                });
        },
        toggleServer(server, identifier, showMsg = true) {
            this.servers[server].toggled = !this.servers[server].toggled;
            this.toggledServers[identifier] = !this.toggledServers[identifier]
            console.log(this.toggledServers)
            if (false === this.toggledServers[identifier]) {
                if (showMsg) {
                    this.changeMessage(`Hiding new actions from ${identifier}`);
                }
            } else {
                if (showMsg) {
                    this.changeMessage(`Showing new actions from ${identifier}`);
                }
            }
        },
        mute() {
            this.muted = !this.muted;
        },
        toggleNewTickets() {
            this.newTickets = !this.newTickets;
            if (this.newTickets) {
                this.changeMessage("Only polling for newly opened tickets");
            } else {
                this.changeMessage("Polling for all ticket actions");
            }
        },
        toggleNoAdmins() {
            this.noAdmins = !this.noAdmins;
            if (this.noAdmins) {
                this.changeMessage(
                    "Only polling for actions from servers without any admins"
                );
                for (const [key, value] of Object.entries(this.servers)) {
                    if (value.admins > 0 && value.admins) {
                        this.toggleServer(key, value.identifier, false);
                        this.changeMessage(`Only polling for ${this.newTickets ? 'new tickets' : 'actions'} from servers with no admins online`);
                    }
                }
            } else {
                this.unToggleServers();
                this.changeMessage("Polling for actions from all servers");
            }
        },
        fetchInitialTickets() {
            fetch(initialTicketUrl, {
                method: "GET",
                headers: {
                    "Content-Type": "application/json",
                },
            })
                .then((res) => res.json())
                .then((res) => {
                    this.tickets = res.data;
                    this.tickets.forEach((t, index) => {
                        t.isNew = false
                        t.timestamp = DateTime.fromSQL(t.timestamp.date, { zone: t.timestamp.timezone })
                        t.relativeTime = t.timestamp.toRelative()
                    })
                    this.lastTicket = this.tickets[0].timestamp
                    console.log(`The most recent ticket timestamp is ${this.lastTicket.toFormat(SQLFormat)}`)
                });
        },
        pollForTickets() {
            this.changeMessage("Checking for new tickets...");
            console.log(`Checking for new tickets since ${this.lastTicket.toFormat(SQLFormat)}`)
            fetch(pollUrl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    since: this.lastTicket.toFormat(SQLFormat),
                }),
            })
                .then((res) => res.json())
                .then((res) => {
                    this.canBwoink = false;
                    if (0 == res.data.length) {
                        this.changeMessage("No new tickets!");
                    } else {
                        this.lastTicket = DateTime.fromSQL(res.data[0].timestamp.date, { zone: res.data[0].timestamp.timezone }) 
                        console.log(`The next check will look for tickets sent since ${this.lastTicket.toFormat(SQLFormat)}`)
                    }
                    res.data.reverse()
                    res.data.forEach((d, index) => {
                        d.timestamp = DateTime.fromSQL(d.timestamp.date, { zone: d.timestamp.timezone })
                        d.isNew = true

                        if (!this.toggledServers[d.server.identifier]) {
                            console.log(
                                `This is a ticket for ${d.server.identifier}, but this server is not toggled so we are discarding it`
                            );
                            res.data.splice(index, 1)
                            return
                        } else if (this.newTickets && "Ticket Opened" != d.action) {
                            console.log(
                                `Only polling for new tickets. This is not a new ticket, so we are discarding it.`
                            );
                            res.data.splice(index, 1)
                            return
                        } else {
                            this.changeMessage("Found some new tickets!");
                            this.canBwoink = true
                            console.log(typeof(this.tickets))
                            this.tickets.unshift(d)
                        }
                    })
                    if (!this.muted && this.canBwoink) {
                        this.bwoink();
                    }
                    this.tickets.forEach((t, index) => {
                        t.relativeTime = t.timestamp.toRelative()
                    })
                });
        },
        bwoink() {
            if (!this.muted) {
                var audio = new Audio("/assets/sound/adminhelp.ogg");
                audio.muted = this.muted;
                audio.play();
            }
        },
        changeMessage(m) {
            this.messages = {
                text: m,
            };
        },
        changeServer(event) {
            var value = event.target.value;
            if ("all" === value) {
                this.changeMessage(`Polling for tickets on all servers`);
                this.server.serverdata.port = null;
                return;
            }
            console.log(value);
            this.server = this.servers[value];
            this.changeMessage(
                `Only polling for tickets from ${this.server.serverdata.servername}`
            );
        },
        unToggleServers() {
            this.toggledServers = [];
            for (const [key, value] of Object.entries(this.servers)) {
                this.servers[key].toggled = true;
            }
        },
        roundDuration(duration) {
            return duration;
        },
        purgeTickets() {
            this.tickets = []
            this.changeMessage("Cleared all tickets")
        }
    },
    //https://developers.google.com/web/updates/2012/01/Web-Audio-FAQ#q_i%E2%80%99ve_made_an_awesome_web_audio_api_application_but_whenever_the_tab_its_running_in_goes_in_the_background_sounds_go_all_weird
    // mounted() -> setInterval() {runs in the same context as setTimeout, async) -> pollForTickets() [async] -> bwoink() -> new Audio().play()
    mounted() {
        this.fetchInitialTickets();
        this.fetchServerList();
        this.interval = setInterval(
            function () {
                this.fetchServerList();
                this.pollForTickets();
            }.bind(this),
            10000
        );
    },
    created: function () {
        this.DateTime = DateTime;
    },
};
</script>