<template>
    <div class="list-group-item row d-flex m-0">
        <dt class="col-1 border-end ps-0">
            <a class="icon-link" :href="'/tgdb/ticket/' + t.round + '/' + t.ticket" target="_blank" rel="noopener noreferrer">#{{
                t.round }}-{{ t.ticket }}</a> on
                <gameLink :server="t.server"></gameLink><br>
            <span class="block"><time>{{ t.relativeTime }}</time><br>
            </span>
        </dt>
        <dd class="col-11 pe-0">
            <span class="whitespace-nowrap border-b border-gray-300 dark:border-gray-700 pb-2">
                <i class="fas pe-1" :class="{
                    'fa-ticket text-info': 'Ticket Opened' === t.action,
                    'fa-reply text-warning': 'Reply' === t.action,
                    'fa-check-circle success': 'Resolved' === t.action,
                    'fa-undo text-danger': 'Rejected' === t.action,
                    'fa-times-circle text-danger': 'Closed' === t.action,
                    'fa-gavel text-info': 'IC Issue' === t.action,
                    'fa-window-close': 'Disconnected' === t.action,
                    'fa-network-wired': 'Reconnected' === t.action,
                }"></i>
                <span> {{ t.action }} by </span>
            <userBadge v-if="t.senderBadge" :user="t.senderBadge" :key="t.id"></userBadge>
            <span v-if="t.recipientBadge"> to </span>
            <userBadge v-if="t.recipientBadge" :user="t.recipientBadge" :key="t.id"></userBadge>
            </span>
            <p class="text-xl mt-2" v-html="t.message"></p>
        </dd>
    </div>
</template>

<script>

import userBadge from "./../common/userBadge.vue";
import gameLink from "./../common/gameLink.vue";

export default {
    components: {
        userBadge,
        gameLink,
    },
    props: {
        t: {
            required: true,
            type: Object,
        },
    },
};
</script>