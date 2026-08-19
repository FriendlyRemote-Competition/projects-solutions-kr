import {computed, ref, watch} from "vue";
import {syncStorage} from "@/utils.js";
import dataJson from '@/assets/data.json';

/* progress in localStorage */
/* [sectionId, ...] */
export const progress = ref([]);
syncStorage(progress, 'progress');

/* bookmarks in localStorage */
/* [{sectionId, contentId = null}, ...] */
export const bookmarks = ref([]);
syncStorage(bookmarks, 'bookmarks');

/* app setting ref */
export const settings = ref({fontSize: 'medium', theme: 'light', lineHeight: 1.5, textWidth: 50});
syncStorage(settings, 'settings');

/* theme watch to color theme */
watch(() => settings.value.theme, () => {
    document.body.setAttribute('data-bs-theme', settings.value.theme);
}, {immediate: true});

/* json data */
export const data = ref(dataJson);

/* json data computed in book, chapters */
export const book = computed(() => data.value?.book);
export const chapters = computed(() => data.value?.chapters);
/* json data computed in book, chapters */

/* parsing reading style by settings */
export const currentReadingStyle = computed(() => {
    let fontSize;
    if (settings.value.fontSize === 'small') fontSize = '.85rem';
    if (settings.value.fontSize === 'medium') fontSize = '1rem';
    if (settings.value.fontSize === 'large') fontSize = '1.2rem';

    return {
        fontSize,
        lineHeight: settings.value.lineHeight,
        maxWidth: settings.value.textWidth + 'ch'
    }
})

/* highlight mark */
export const highlight = ref(null);