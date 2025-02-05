<template>
    <div>
        <overlay v-if="loading"></overlay>
        <h2 class="noChats" v-if="usersWithMatch.length == 0">No matches, no messages—yet! Swipe more to find great exchanges.</h2>
        <matchCard
                    v-for="(user, index) in usersWithMatch"
                    :key="index"
                    :chatId="user.chatId"
                    :userName="user.name"
                    :image="user.profile_picture"
                    :offer="user.offers_count"
                    :message="user.unread_messages_count"
                    @selectChat="selectChat(user)"
                />
    </div>
</template>

<script setup>
    import matchCard from "./components/matchCard.vue";
    import { ref, reactive, onMounted, onBeforeUnmount } from "vue";
    import swapApiResource from "../api/swapResource";
    import { useRouter } from 'vue-router';
    import feather from "feather-icons";

    import overlay from "./components/overlay.vue";
    import { useStore } from 'vuex'

    const store = useStore();

    const router = useRouter()
    const swapResource = new swapApiResource();
    const loading = ref(false);

    onBeforeUnmount(() => {
        store.commit("setLoading", true);
    })

    onMounted( async () => {
        feather.replace();

        //Messages
        await swapResource
            .getUserChats()
            .then((response) => {
                usersWithMatch.value = response;
                console.log(usersWithMatch.value)
            });

        store.commit("setLoading", false);
    });

    const usersWithMatch = ref([]);

    const selectChat = (user) => {
      console.log('selectChat', user);
      router.push({
        name: 'messages',
        query: {
            chatId: user.chat_id,
            userName: user.name,
            image: user.profile_picture,
            userId: user.userId
        }
      });
    };

</script>

<style>

.noChats {
    display: block;
    text-align: center;
    color: rgba(107, 148, 107, 0.596);
    font-family: monospace; /* Ensures even spacing */
    white-space: pre-line; /* Keeps line breaks */
}


</style>
