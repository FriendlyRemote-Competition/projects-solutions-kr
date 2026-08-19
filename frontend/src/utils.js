import {isRef, watch} from "vue";

/* localstorage sync to vue ref, reactive value */
export const syncStorage = (value, name) => {
    const storageName = 'kr_storage_' + name;

    if(localStorage[storageName]) {
        if(isRef(value)) {
            value.value = JSON.parse(localStorage[storageName]);
        } else {
            Object.assign(value, JSON.parse(localStorage[storageName]));
        }
    }

    watch(value, () => {
        localStorage[storageName] = isRef(value) ? JSON.stringify(value.value) : JSON.stringify(value);
    }, {deep: true});
}

/* vite image parsing */
export const getImage = url => import.meta.env.BASE_URL + '/' + url;

/* get only number text */
export const getNumber = text => text.replace(/[^0-9]/g, '');

/* get excerpt in section content */
export const getExcerpt = (text, search, radius = 50) => {
    const i = text.toLowerCase().indexOf(search.toLowerCase());
    if (i === -1) return text.slice(0, radius * 2) + '…';
    return (i > radius ? '…' : '') + text.slice(Math.max(0, i - radius), i + search.length + radius) + '…';
};

/* explode section content */
export const explodeContent = content => {
    const split = 100;

    const arr = [];
    for(let i = 0; i < content.length; i += split) {
        arr.push(content.slice(i, i + split));
    }

    return arr;
}

/* set mark tag replace */
export const setMarked = (text, search) => search ? text.replace(new RegExp(`(${search})`, 'gi'), '<mark>$1</mark>') : text;