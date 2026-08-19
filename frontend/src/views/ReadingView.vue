<script setup>

import {computed, onMounted, onUnmounted} from "vue";
import {bookmarks, chapters, currentReadingStyle, data, highlight, progress} from "@/stores/app.js";
import {explodeContent, getImage, setMarked} from "@/utils.js";
import router from "@/router/index.js";

const {chapterId, sectionId} = defineProps(['chapterId', 'sectionId']);

/* current chapter and section computed get by params */
const chapter = computed(() => chapters.value?.find(c => c.id === chapterId));
const section = computed(() => chapter.value?.sections.find(s => s.id === sectionId));
/* current chapter and section computed get by params */

/* progress push to first visited this section */
onMounted(() => {
  if (section.value && section.value.id) {
    if (!progress.value.includes(section.value.id)) {
      progress.value.push(section.value.id);
    }
  }
});

/* section parsing */
const currentSectionIndex = computed(() => chapter.value?.sections.findIndex(s => s.id === section.value.id));

const successSections = computed(() => chapter.value?.sections.filter(s => progress.value.includes(s.id)).length ?? 0)

const findSectionIndex = computed(() => chapter.value?.sections.findIndex(s => s.id == sectionId));

/* section prev and next pagination functions */
function goPrev() {
  const index = Math.max(0, currentSectionIndex.value - 1);
  return router.push(`/read/${chapter.value.id}/${chapter.value.sections[index].id}`);
}

function goNext() {
  const index = Math.min(chapter.value.sections.length - 1, currentSectionIndex.value + 1);
  return router.push(`/read/${chapter.value.id}/${chapter.value.sections[index].id}`);
}
/* section prev and next pagination functions */


/* calc progress */
const chapterProgress = computed(() => ((successSections?.value ?? 0) / (chapter.value?.sections.length ?? 0) * 100).toFixed(2));

/* reset chapter progress */
function resetProgressThisChapter() {
  const currentSections = chapter.value.sections.map(s => s.id);

  progress.value = progress.value.filter(p => !currentSections.includes(p));
  return router.push(`/read/${chapter.value.id}/${chapter.value.sections[0].id}`);
}

/* bookmark setting */
function setBookmark(idx = null) {
 bookmarks.value.push({sectionId, contentIdx: idx});
}

onUnmounted(() => highlight.value = null);
</script>

<template>
  <RouterLink to="/" class="btn btn-success me-auto">← Library</RouterLink>
  <template v-if="data">
    <div class="hstack justify-content-between flex-wrap gap-3">
      <div class="hstack gap-2 align-items-end">
        <h2 class="mb-0">Chapter Reading</h2>
        <span class="text-muted">Chapter {{ chapter.number }} → Section {{ findSectionIndex + 1 }} of {{ chapter.sections.length }}</span>
      </div>

      <div class="hstack gap-2">
        <button class="btn btn-danger" @click="resetProgressThisChapter()">Reset Progress This Chapter</button>
        <button class="btn btn-primary" :class="{'item-disabled': bookmarks.some(b => b.sectionId == sectionId)}" @click="setBookmark(null)">⭐ Bookmark</button>
        <button class="btn btn-warning">Aa</button>
      </div>
    </div>
    <div class="vstack gap-2">
      <small>Chapter Progress: {{ chapterProgress }}%</small>
      <div class="progress">
        <div class="progress-bar" :style="{width: chapterProgress + '%'}"></div>
      </div>
    </div>
    <div class="row g-3">
      <div class="col-md-4 col-12">
        <div class="list-item vstack gap-2">
          <h3 class="fs-5">TABLE OF CONTENTS</h3>
          <div class="vstack gap-3">
            <template v-for="(item, idx) in chapter.sections">
              <RouterLink :to="`/read/${chapter.id}/${item.id}`" class="btn text-start list-item fill"
                          :class="{active: item.id === sectionId, clear: progress.includes(item.id)}">{{ idx + 1 }}.
                Section {{ idx + 1 }}
              </RouterLink>
            </template>
          </div>
        </div>
      </div>
      <div class="col-md-8 col-12">
        <div class="list-item vstack gap-2" :style="{...currentReadingStyle}">

          <template v-if="highlight">
            <h3 v-html="setMarked(section.heading, highlight)"></h3>
            <p>
              <template v-for="(c,idx) in explodeContent(section.content)">
                <div class="explode-text d-inline">
                  <span v-if="bookmarks.some(s => s.sectionId == sectionId && s.contentIdx == idx)">⭐</span>
                  <span v-html="setMarked(c, highlight)"></span>
                  <button @click="setBookmark(idx)" class="btn btn-primary text-nowrap btn-sm">⭐ Bookmark</button>
                </div>
              </template>
            </p>
          </template>
          <template v-else>
            <h3>{{ section.heading }}</h3>
            <p>
            <template v-for="(c,idx) in explodeContent(section.content)">
              <div class="explode-text d-inline">
                <span v-if="bookmarks.some(s => s.sectionId == sectionId && s.contentIdx == idx)">⭐</span>
                <span>{{ c }}</span>
                <button @click="setBookmark(idx)" class="btn btn-primary text-nowrap btn-sm">⭐ Bookmark</button>
              </div>
            </template>
            </p>
          </template>

          <img v-if="section.image" :src="getImage(section.image)" class="w-100 rounded" :alt="section.imageAlt">
          <div class="list-item fill" v-else>No Image :(</div>

          <div class="hstack justify-content-between">
            <button class="btn outline text-primary" @click="goPrev()">← Previous</button>
            <button class="btn outline text-primary" @click="goNext()">Next →</button>
          </div>
        </div>
      </div>
    </div>
  </template>
</template>

<style scoped>
.list-item.clear:not(.active) {
  color: white;
  background: var(--bs-success);
  font-weight: bold;
}

.list-item.active {
  background: var(--bs-primary);
  color: white;
  font-weight: bold;
}

.explode-text {
  cursor: pointer;
  transition: .3s;
  position: relative;
}

.explode-text .btn {
  position: absolute;
  left: 0;
  top: 0;
  opacity: 0;
  visibility: hidden;
  transition: .3s;
}

.explode-text:hover {
  color: var(--bs-primary);
}

.explode-text:hover .btn {
  opacity: 1;
  visibility: visible;
}
</style>