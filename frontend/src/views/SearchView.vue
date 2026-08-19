<script setup>
import {computed, ref} from "vue";
import {chapters, highlight} from "@/stores/app.js";
import {getExcerpt, getNumber, setMarked} from "@/utils.js";
import router from "@/router/index.js";

const {search} = defineProps(['search']);

/* current searched chapter */
const searchChapters = computed(() => chapters.value.flatMap(c => c.sections)?.filter(s => s.heading.toLowerCase().includes(search.toLowerCase()) || s.content.toLowerCase().includes(search.toLowerCase())));

const searchValue = ref("");

/* router push to another search value */
function moveSearch() {
  return router.push(`/search/${searchValue.value}`);
}

/* go section */
function moveItem(item) {
  highlight.value = search;
  return router.push(`/read/${item.id.split('-')[0]}/${item.id}`);
}
</script>

<template>
  <RouterLink to="/" class="btn btn-success me-auto">← Library</RouterLink>
<h2>Search screen</h2>
  <form @submit.prevent="moveSearch" class="hstack gap-2">
    <input type="text" class="form-control" v-model="searchValue" id="search" aria-label="search" placeholder="Search...">
    <button class="btn btn-primary">Search</button>
  </form>
  <template v-if="searchChapters?.length">
    <span aria-live="polite">Find {{searchChapters.length}} result</span>
    <div class="row row-cols-1 g-3">
      <div class="col" v-for="item in searchChapters" :key="item.id">
        <div style="cursor: pointer" @click="moveItem(item)" class="d-block list-item vstasck gap-3">
          <h3 class="fs-5">Chapter {{getNumber(item.id.split('-')[0])}} → Section {{getNumber(item.id.split('-')[1])}}. <span v-html="setMarked(item.heading, search)"></span></h3>
          <p v-html="setMarked(getExcerpt(item.content, search), search)"></p>
        </div>
      </div>
    </div>
  </template>
  <span v-else>No results found.</span>
</template>

<style scoped>

</style>