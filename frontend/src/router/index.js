import {createRouter, createWebHistory} from 'vue-router'
import LibraryView from "@/views/LibraryView.vue";
import ReadingView from "@/views/ReadingView.vue";
import SearchView from "@/views/SearchView.vue";
import BookmarksView from "@/views/BookmarksView.vue";
import NotFoundView from "@/views/NotFoundView.vue";
import SettingView from "@/views/SettingView.vue";
import {chapters, progress} from "@/stores/app.js";

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/',
            component: LibraryView
        },
        {
            path: '/read/:chapterId',
            redirect: (to) => { // first read chapter section redirect to first section
                const chapter = chapters.value?.find(c => c.id === to.params.chapterId); // find section
                if(!chapter) return {name: 'not-found'}; // not found to redirect
                let target = chapter.sections.filter(s => progress.value.includes(s.id));

                if(!target.length) target = chapter.sections[0];
                else target = target.at(-1);

                return `/read/${chapter.id}/${target.id}`;
            }
        },
        {
            path: '/read/:chapterId/:sectionId',
            component: ReadingView,
            props: true
        },
        {
            path: '/search/:search',
            component: SearchView,
            props: true
        },
        {
            path: '/bookmarks',
            component: BookmarksView
        },
        {
            path: '/settings',
            component: SettingView
        },
        {
            path: '/:pathMatch(.*)*',
            name: 'not-found',
            component: NotFoundView
        },
    ]

})

export default router
