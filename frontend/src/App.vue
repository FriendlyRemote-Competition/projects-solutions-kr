<script setup>
import AppHeader from "@/components/AppHeader.vue";
import {getImage} from "@/utils.js";
import {book} from "@/stores/app.js";
</script>

<template>
  <AppHeader></AppHeader>
  <main class="container vstack gap-3">
    <img :src="getImage(book.coverImage)" v-if="book.coverImage" class="cover-image" :alt="getImage(book.coverImageAlt)">
    <RouterView v-slot="{Component, route}">
      <component :is="Component" :key="route.params.sectionId ?? route.name"/>
    </RouterView>
  </main>
</template>

<style scoped>
.cover-image {
  position: fixed;
  inset: 0;
  object-fit: cover;
  z-index: -1;
  opacity: .3;
}
</style>
