<script setup>

import {book, data} from "@/stores/app.js";
import {ref} from "vue";

// search input value
const searchValue = ref('');
</script>

<template>
<header>
  <div class="hstack justify-content-between container h-100 flex-wrap gap-3" v-if="data">
    <div class="vstack justify-content-center">
      <h1 class="fs-3 mb-0">{{book?.title ?? ''}}</h1>
      <span class="text-muted">{{book?.subtitle ?? ''}}</span>
    </div>

    <div class="hstack gap-4">
      <div class="hstack gap-2">
        <input type="text" class="form-control" placeholder="search..." id="search-input" v-model="searchValue" aria-label="search-input">
        <RouterLink :to="`/search/${searchValue}`" @click="searchValue = ''" class="btn btn-primary">Search</RouterLink>
      </div>
      <RouterLink to="/settings" class="btn btn-success">Settings</RouterLink>
    </div>

  </div>
</header>
</template>

<style scoped>
@media (max-width: 1281px) {
  header {
    padding: 25px 0;
    height: auto;
  }
}
</style>