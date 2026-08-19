<script setup>

import {bookmarks, chapters} from "@/stores/app.js";
import {explodeContent, getNumber} from "@/utils.js";
import router from "@/router/index.js";

/* section and content find function */
const getSection = item => chapters.value.flatMap(c => c.sections).find(c => c.id == item.sectionId);

const getContent = item => explodeContent(getSection(item).content)[item.contentIdx];
/* section and content find function */

/* move to section */
function goTo(item) {
  return router.push(`/read/${item.sectionId.split('-')[0]}/${item.sectionId}`);
}

/* selected bookmark remove */
function remove(idx) {
  bookmarks.value.splice(idx, 1);
}
</script>

<template>
  <RouterLink to="/" class="btn btn-success me-auto">← Library</RouterLink>
  <h2>My bookmarks ({{ bookmarks.length }})</h2>

  <template v-if="bookmarks.length">
    <div class="row g-3 row-cols-1">
      <div class="col" v-for="(item,idx) in bookmarks">
        <div class="list-item hstack justify-content-between">
          <div class="vstack gap-2">
            <span class="badge me-auto text-bg-secondary" v-if="item.contentIdx">Content Bookmarked</span>
            <span class="badge me-auto text-bg-primary" v-else>Section Bookmarked</span>
            <h3>Chapter {{ getNumber(item.sectionId.split('-')[0]) }} → Section
              {{ getNumber(item.sectionId.split('-')[1]) }}</h3>
            <span v-if="item.contentIdx">
              ...{{ getContent(item) }}....
            </span>
          </div>

          <div class="hstack gap-2">
            <button class="btn btn-primary" @click="goTo(item)">Go to</button>
            <button class="btn btn-danger" @click="remove(idx)">Remove</button>
          </div>
        </div>
      </div>
    </div>
  </template>
  <span v-else>You don't have any bookmarks yet. Go section or section content to bookmark</span>
</template>

<style scoped>

</style>