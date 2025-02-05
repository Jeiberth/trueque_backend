<template>
    <div>
        <div class="card">
            <div class="card-image" @click="openProfile">
                <img :src="userImage" alt="Description" class="card-image-img" />
            </div>
            <div class="card-body" @click="openProfile" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                <div class="user-info">
                    <h5 class="userName mt-0">{{ userName }}</h5>
                </div>
            </div>
            <div class="card-circle">
                <div
                class="tab"
                :class="{ active: selectedTab === 'chats' }"
                @click="openMessages('chats')"
                >
                <i data-feather="message-square"></i>
                </div>
                <div
                class="tab tab-swap"
                :class="{ active: selectedTab === 'swaps' }"
                @click="openSwaps('swaps')"
                style="background-color: white"
                >
                <i data-feather="shuffle"></i>
                </div>
            </div>
        </div>

        <router-view v-slot="{ Component }">
            <transition name="fade" mode="out-in">
            <component :is="Component" />
            </transition>
        </router-view>
    </div>
</template>


<script setup>
    import { ref, onMounted, reactive, onBeforeUnmount } from "vue";
    import feather from "feather-icons";
    import swapApiResource from "../../api/swapResource"
    import { useRoute, useRouter } from "vue-router";

    // Using useRoute to access route params
    const route = useRoute();
    const router = useRouter();


    const selectedTab = ref("chats");
    const swapResource = new swapApiResource();

    const userName = route.query.userName;
    const userImage = route.query.image;
    const userId = route.query.userId;
    const chatId = route.query.chatId;

    onMounted(() => {
        feather.replace();
    });

    // Emit functions
    const openProfile = () => {
        console.log("openProfile");
        router.push({ name: "seeProfile", query: { userId: userId } });
    };

    const openMessages = (tab) => {
        console.log("openMessages");
        router.push({ name: "messages",query: { chatId: chatId, userName: userName, image: userImage, userId: userId} });
        selectedTab.value = tab;
    };

    const openSwaps = (tab) => {
        console.log("openSwaps");
        router.push({ name: "offers", query: { chatId: chatId, userId: userId } });
        selectedTab.value = tab;
    };

</script>

<style scoped>

    .card {
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.219);
    display: flex;
    height: 90px;
    width: 96%;
    margin-left: 2%;
    margin-top: 10px;
    align-items: center;
    justify-content: space-between; /* Space between children */
    border-radius: 50px;
    flex-direction: row;
    background-color: white;
    }

    .card-image {
    display: inline;
    align-self: flex-start;
    margin-right: 10px;
    }

    .card-image-img {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    margin: 0;
    }

    .card-body {
    display: inline-flex;
    flex-grow: 1; /* Allow card-body to grow */
    padding: 0;
    }

    .userName {
    margin: 4px;
    font-weight: 600;
    font-size: larger;
    padding-bottom: 0px;
    margin-bottom: 0px;
    height: 30px;
    }

    .card-circle {
    margin-right: 10px; /* Add margin to the left */
    display: flex;
    justify-content: center;
    align-items: center;
    }

    .tab {
    text-align: center;
    cursor: pointer;
    padding: 10px;
    transition:
        background-color 1s ease,
        color 1s ease,
        box-shadow 1s ease,
        border 1s ease;
    border: 2px solid transparent;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.219);
    border-radius: 20px;
    margin: 0 5px;
    width: 50px;
    height: 50px;
    }

    .tab.active {
    background-color: white;
    color: black;
    border: 2px solid darkslategray;
    box-shadow:
        inset 0 4px 15px rgba(65, 155, 95, 0.5),
        0 4px 15px rgba(0, 0, 0, 0.219);
    }

    .fade-enter-active,
    .fade-leave-active {
    transition: opacity 0.3s ease, transform 0.3s ease;
    }

    .fade-enter-from,
    .fade-leave-to {
    opacity: 0;
    transform: translateY(10px);
    }
</style>
