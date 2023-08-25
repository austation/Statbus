<template>
    <span
      class="badge"
      :style="{
        backgroundColor: user.backColor,
        color: user.foreColor,
      }"
      :title="user.title"
    >
      <i class="fa fa-fw" :class="user.icon"></i>
      {{ user.ckey }}
    </span>
  </template>
  
  <script>
  export default {
    props: {
      user: {
        required: true,
        type: Object,
      },
    },
  };
  </script>