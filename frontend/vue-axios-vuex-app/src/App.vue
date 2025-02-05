<template>
    <!-- Display the overlay if loading is true -->
    <overlay v-if="loading"></overlay>
    <router-view></router-view>
</template>

<script setup>
    import { useStore } from 'vuex';
    import { computed, onMounted } from 'vue';
    import overlay from "./views/components/overlay.vue";

    const store = useStore();

    // Use computed to bind to the reactive Vuex state
    const loading = computed(() => store.getters.getLoading);

    onMounted(() => {
      store.dispatch('fetchData');
    });

</script>
