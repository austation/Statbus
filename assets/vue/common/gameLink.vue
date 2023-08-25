<template>
    <a class="icon-link" v-if="server.gameLink" :href="server.gameLink"
      ><i class="fa fa-fw fa-gamepad"></i> {{ server.identifier }}</a
    >
    <span v-else> {{ server.identifier }}</span>
  </template>
  
  <script>
  export default {
    props: {
      server: {
        required: true,
        type: Object,
      },
    },
  };
  </script>