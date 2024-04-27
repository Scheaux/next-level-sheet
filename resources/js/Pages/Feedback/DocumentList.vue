<script setup>
import { Head } from '@inertiajs/vue3';
</script>

<script>
export default {
    data: () => ({
        documents: [],
        page: 0,
        lastPage: '',
    }),

    mounted() {
        this.getDocuments();
    },

    watch: {
        page(newVal) {
            this.getDocuments(newVal);
        },
    },

    methods: {
        async getDocuments(page) {
            const { data } = await axios.get(`docs?page=${page}`);
            this.page = data.current_page;
            this.lastPage = data.last_page;
            this.documents = data.data;
        },

        openUrl(url) {
            window.open(url, '_blank');
        },
    },
};
</script>

<template>
    <Head title="Documents" />

    <v-sheet class="mx-auto" width="600">
        <v-expansion-panels>
            <v-expansion-panel v-for="document in documents">
                <v-expansion-panel-title>{{
                    document.firstName + ' ' + document.lastName
                }}</v-expansion-panel-title>
                <v-expansion-panel-text
                    ><v-btn
                        v-if="document.sheetUrl"
                        color="success"
                        @click="openUrl(document.sheetUrl)"
                        >view in google sheets</v-btn
                    >
                    <span v-if="!document.sheetUrl"
                        >(empty)</span
                    ></v-expansion-panel-text
                >
            </v-expansion-panel>
        </v-expansion-panels>

        <v-pagination v-model="page" :length="lastPage"></v-pagination>
    </v-sheet>
</template>
