<script setup>

import {chapters, currentReadingStyle, data, settings} from "@/stores/app.js";
import {getImage} from "@/utils.js";
import {computed} from "vue";

/* test sample section content */
const sampleSection = computed(() => chapters.value[0].sections[0]);
</script>

<template>
  <RouterLink to="/" class="btn btn-success me-auto">← Library</RouterLink>
  <h2>Settings</h2>
  <div class="row g-3" v-if="data">
    <div class="col-md-8 col-12">
      <div class="list-item fill vstack gap-3">
        <h3>Sample Reading Screen</h3>
        <div class="list-item" :style="{...currentReadingStyle}">
          <h4 class="fs-3">{{sampleSection.heading}}</h4>
          <p>{{sampleSection.content}}</p>
          <img v-if="sampleSection.image" :src="getImage(sampleSection.image)" class="w-100 rounded" :alt="sampleSection.imageAlt">
        </div>
      </div>
    </div>
    <div class="col-md-4 col-12">
      <div class="list-item vstack gap-4">
        <h3>Reading settings</h3>

        <div class="vstack gap-2">
          <h4 class="fs-5">FONT SIZE</h4>
          <div class="hstack gap-2 list-item p-2">
            <button @click="settings.fontSize = 'small'" :class="{'btn-primary': settings.fontSize === 'small', 'fill' : settings.fontSize !== 'small'}" class="btn">Small</button>
            <button @click="settings.fontSize = 'medium'" :class="{'btn-primary': settings.fontSize === 'medium', 'fill' : settings.fontSize !== 'medium'}" class="btn">Medium</button>
            <button @click="settings.fontSize = 'large'" :class="{'btn-primary': settings.fontSize === 'large', 'fill' : settings.fontSize !== 'large'}" class="btn">Large</button>
          </div>
        </div>

        <div class="vstack gap-2">
          <h4 class="fs-5">COLOUR THEME</h4>
          <div class="hstack gap-2 list-item p-2">
            <button @click="settings.theme = 'light'" :class="{'btn-primary': settings.theme === 'light', 'fill' : settings.theme !== 'light'}" class="btn">Light</button>
            <button @click="settings.theme = 'dark'" :class="{'btn-primary': settings.theme === 'dark', 'fill' : settings.theme !== 'dark'}"  class="btn">Dark</button>
          </div>
        </div>

        <div class="vstack gap-2">
          <h4 class="fs-5">LINE SPACING ({{ settings.lineHeight }})</h4>
          <input type="range" class="form-range" min="1" max="2" step="0.1" v-model="settings.lineHeight">
        </div>

        <div class="vstack gap-2">
          <h4 class="fs-5">TEXT BLOCK WIDTH ({{ settings.textWidth }})</h4>
          <input type="range" class="form-range" min="30" max="100" step="1" v-model="settings.textWidth">
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>