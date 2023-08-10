<template>
    <table class="table table-bordered table-responsive">
        <thead>
            <tr>
                <th>Discord ID</th>
                <th>Timestamp</th>
                <th>Is Current?</th>
                <th>Discord Username</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="d in data">
                <td>{{ d.discord_id }}</td>
                <td>{{ d.timestamp.date }}</td>
                <td class="text-center" :class="{'text-success': d.valid, 'text-danger': !d.valid}">
                    <i class="fa-solid" :class="{'fa-circle-check': d.valid, 'fa-circle-xmark': !d.valid}"></i>
                </td>
                <td v-if="d.user" style="background: rgb(88, 101, 242);">
                    <a :href="'https://discordapp.com/channels/@me/' + d.discord_id" target="_blank" class="text-white">{{ d.user.username }}</a>
                </td>
                <td v-else></td>
            </tr>
        </tbody>
    </table>
</template>

<script>


export default {
    data () {
            return {
                url: null,
                data: {}
            }
        },
    methods: {
        fetchDiscordInformation() {
            this.url = document.getElementById('discord').dataset.url
            fetch(this.url, {
                method: "GET",
                headers: {
                    "Content-Type": "application/json",
                },
            })
                .then((res) => res.json())
                .then((res) => {
                    this.data = res.discord;
                });
        },
    },
    mounted() {
        this.fetchDiscordInformation();
    }
};
</script>