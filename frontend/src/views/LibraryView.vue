<script setup>

import {chapters, data, progress} from "@/stores/app.js";

/* calc progress function */
const getProgress = item => {
  const length = item.sections.length;
  const sIds = item.sections.map(s => s.id);

  const count = progress.value.filter(p => sIds.includes(p)).length;

  return (count / length * 100).toFixed(2);
}

/* parsing section current position */
const getCurrentPosition = item => {
  return item.sections.filter(i => progress.value.includes(i.id)).length;
}
</script>

<template>
  <div class="hstack justify-content-between align-items-start">
    <h2>Library</h2>
    <RouterLink to="/bookmarks" class="btn btn-primary">Go Bookmark List</RouterLink>
  </div>
  <div class="row g-3 row-cols-1 row-cols-md-2 row-cols-lg-3" v-if="data">
    <div class="col" v-for="item in chapters" :key="item.id">
      <div class="list-item vstack gap-2" :class="{active: getProgress(item) == '100.00'}">
        <h3 class="fs-5">Chapter {{item.number}}. {{item.title}}</h3>

        <div class="hstack gap-2">
          <div class="progress-bar bg-secondary-subtle rounded w-75">
            <div class="progress bg-primary" :style="{width: getProgress(item) + '%'}"></div>
          </div>
          <span class="text-muted">{{getProgress(item)}}% read</span>
        </div>
        <small>Current Position: Chapter {{item.number}} -> Section {{getCurrentPosition(item)}}</small>

        <RouterLink :to="`/read/${item.id}`" class="btn outline text-primary ms-auto">View</RouterLink>
      </div>
    </div>
  </div>
</template>

<style scoped>
.list-item.active {
  border: 3px solid var(--bs-primary);
  box-shadow: 6px 6px 0 var(--bs-primary);
}
</style>