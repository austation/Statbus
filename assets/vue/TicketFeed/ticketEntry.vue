<template>
    <a class="list-group-item row m-0 px-0" :href="`/tgdb/tickets/${t.round}/${ t.ticket}`">
        <dt class="d-flex justify-content-between align-items-center">
            <div>
            <a class="icon-link" :href="'/tgdb/ticket/' + t.round + '/' + t.ticket" target="_blank" rel="noopener noreferrer">#{{
                t.round }}-{{ t.ticket }}</a> on <gameLink :server="t.server"></gameLink> 
                <i class="ms-2 fas pe-1" :class="`${t.action.icon} text-${t.action.cssClass}`"></i>
                <span v-if="t.action.isConnectAction">
                    <userBadge v-if="t.recipientBadge" :user="t.recipientBadge" :key="t.id"></userBadge> {{ t.action.action }}
                </span>
                <span v-else-if="t.action.isAction">
                    {{ t.action.action }} by <userBadge v-if="t.senderBadge" :user="t.senderBadge" :key="t.id"></userBadge>
                </span>
                <span v-else>
                    {{ t.action.action }} by <userBadge :user="t.senderBadge" :key="t.id"></userBadge>
                    <span v-if="t.recipientBadge">
                        to <userBadge :user="t.recipientBadge"></userBadge></span>
                </span>
            </div>
            <div><time>{{ t.relativeTime }}</time>
            </div>
        </dt>
        <dd class="flex-grow-1">
            <span class="whitespace-nowrap border-b pb-2">
            </span>
            <p v-if="!t.action.isAction" class="mt-2" v-html="t.message"></p>
            <small class="entry-metadata">ID: {{ t.id }} at {{ t.timestamp.toFormat('y-MM-dd TT') }}</small>
        </dd>
    </a>
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