<template>
    <div>
        <overlay v-if="loadingSearching"></overlay>
        <successErrorCard :type="typeSuccessErrorCard" :text="textSuccessErrorCard" :launch="showSuccessErrorCard"></successErrorCard>
        <searchElement @searchThingsEmit="searchThings" @showFiltersEmit="showFiltersEmit()"></searchElement>
        <h2 class="noSwaps" v-if="showThingsAfterSearch && isThingsEmpty">Looks like you've seen everything! Try searching for something new or expanding your range to discover more.</h2>
        <swipeCard v-if="showThingsAfterSearch" :things="thingsForSwipe" @swipeRight="onSwipeRight" @swipeLeft="onSwipeLeft"  @allSwiped="onAllSwiped"/>
    </div>
</template>

<script setup>
    import { ref, reactive, onMounted, onBeforeUnmount, computed } from "vue";
    import Swal from 'sweetalert2'
    import overlay from "../components/overlay.vue";
    import searchElement from "../components/searchElement.vue";
    import swipeCard from "../components/swipeCard.vue";
    import swapApiResource from "../../api/swapResource"
    import feather from "feather-icons";
    import { useStore } from 'vuex'

    const store = useStore();

    onBeforeUnmount(() => {
        store.commit("setLoading", true);
    })


    import successErrorCard from "../components/successErrorCard.vue";
    const typeSuccessErrorCard = ref('');
    const textSuccessErrorCard = ref('');
    const showSuccessErrorCard = ref(false);

    const thingsForSwipe = reactive([]);

    const swapResource = new swapApiResource();
    onMounted(async () => {
        feather.replace();
        //Swipe
        await swapResource
            .getFilteredThings()
            .then((response) => {
                thingsForSwipe.value = response;
                showThingsAfterSearch.value = true;
                console.log(thingsForSwipe.value);
                if(response == null){ onAllSwiped(); };
                store.commit("setLoading", false);
            });

        
    });

    const isThingsEmpty = computed(() => thingsForSwipe.value.length === 0);

    const matchMessages = reactive([
    {
        title: '🎉 Match Found!',
        message: "You've found a swap partner! Ready to exchange your items? Let's make this happen!"
    },
    {
        title: '🤝 It’s a Match!',
        message: "Great news! You both want to swap. Tap below to chat and finalize the exchange!"
    },
    {
        title: '🥳 Swap Time!',
        message: "You matched! Let’s get this swap rolling—hit the button to connect!"
    },
    {
        title: '🚀 Match Alert!',
        message: "You’ve got a match! Don’t miss out—click below to start your swap journey!"
    },
    {
        title: '🎊 It’s a Match!',
        message: "You and your match are ready to swap! Message them to arrange the details!"
    },
    {
        title: '💥 Exciting Match!',
        message: "You found someone to swap with! Let’s make this exchange happen!"
    },
    {
        title: '✨ Match Made!',
        message: "Congratulations! You’ve found a great swap partner. Time to chat!"
    },
    {
        title: '🎈 You Matched!',
        message: "Awesome! You matched with someone interested in swapping. Connect now!"
    },
    {
        title: '🌟 Swap Partner Found!',
        message: "You’ve found a swap partner! Ready to chat and arrange the exchange?"
    },
    {
        title: '🎶 It’s a Perfect Match!',
        message: "What a coincidence! You both want to swap. Let’s get started!"
    },
    {
        title: '🔔 Match Notification!',
        message: "You matched with someone looking to swap. Don’t wait—message them now!"
    },
    {
        title: '🏆 Match Success!',
        message: "Great job! You have a match. Time to coordinate your swap!"
    },
    {
        title: '💖 It’s a Great Match!',
        message: "You’ve matched with a fellow swapper! Let’s talk details!"
    },
    {
        title: '💫 You Matched Up!',
        message: "Fantastic! You found a swap partner. Click below to start chatting!"
    },
    {
        title: '🔥 Hot Match!',
        message: "You’ve got a match! Time to connect and finalize your swap!"
    },
    {
        title: '📦 Swap Partner Alert!',
        message: "You matched with someone ready to swap. Reach out to them now!"
    },
    {
        title: '🚦 Match Confirmed!',
        message: "You’ve found a swap partner! Ready to exchange details?"
    },
    {
        title: '💡 Match Found!',
        message: "Great news! You have a match. Start chatting to arrange the swap!"
    },
    {
        title: '🍀 Lucky Match!',
        message: "You matched with someone who wants to swap. Don’t miss your chance!"
    },
    {
        title: '🔗 Connection Established!',
        message: "You’ve got a match! Let’s connect and make that swap happen!"
    },
    {
        title: '🥇 It’s a Winner!',
        message: "You found a swap partner! Ready to start your exchange journey?"
    }
    ]);

    //swap tap
    const showThingsAfterSearch = ref(false);
    const showFiltersEmit = () => {
        showThingsAfterSearch.value = false;
    }

    const loadingSearching = ref(false); //overlay to show loading
    const searchThings = (obj) => {
        loadingSearching.value = true;
        showThingsAfterSearch.value = false;
        console.log("searchThings", obj);
        swapResource
            .getFilteredThings({ range: obj.range, category_id: obj.category, condition_id: obj.condition , material_id: obj.material , color_id: obj.color , weight: [ 0 , obj.weight ], price: [ 0 , obj.price ], search: obj.searchTerm })
            .then((response) => {
                thingsForSwipe.value = response;
                showThingsAfterSearch.value = true;
                loadingSearching.value = false;
                if(response == null){ onAllSwiped(); };
            });
    };

    const onSwipeRight = (thingId) => {
        swapResource
            .swipeRight({ thing_id: thingId })
            .then((response) => {
                if(response.match){
                    Swal.fire({
                        title: matchMessages[Math.floor(Math.random() * matchMessages.length)].title,
                        text: matchMessages[Math.floor(Math.random() * matchMessages.length)].message,
                        icon: "success",
                        iconHtml: '<i data-feather="shuffle" class="custom-icon"></i>',
                        timer: 2800,
                        timerProgressBar: true,
                        showConfirmButton: false, // Remove the OK button
                        backdrop: true,
                        customClass: {
                            popup: 'custom-modal', // Custom modal class
                            icon: 'custom-icon-wrapper', // Custom icon wrapper class
                        },
                        willOpen: () => {
                            feather.replace(); // Replace <i> tags with Feather icons
                        },
                    });
                }
            });
    };

    const onSwipeLeft = (thingId) => {
        swapResource
            .swipeLeft({ thing_id: thingId })
            .then((response) => {
                console.log("SwipeLeft Recorded Succesfully", response);
            });
    };

    const onAllSwiped = () => {
        typeSuccessErrorCard.value = 'info';
        textSuccessErrorCard.value = 'Try Searching for Something Else';
        showSuccessErrorCard.value = true;
        setTimeout(() => {
            showSuccessErrorCard.value = false;
        }, 2800);
        console.log("AllSwiped");
        thingsForSwipe.value.length = 0;
    };


</script>


<style scoped>
/* Custom styles for the modal */
.custom-modal {
    background-color: white;/* Light background color */
    border-radius: 50px;
    padding: 20px;
    width: 80%;
    height: auto;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.219);
    overflow: hidden;
}

/* Custom styles for the icon wrapper */
.custom-icon-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 70px; /* Larger circle around the icon */
    height: 70px;
    margin-bottom: 10px; /* Space below the icon */
    margin-top: 10px;
    border-radius: 50%;
    animation: rotateIcon 2s infinite linear; /* Rotate animation */
}

.swal2-title{
    padding: 0;
}

/* Style for the icon itself */
.custom-icon {
    width: 50px; /* Width of the icon */
    height: 50px; /* Height of the icon */
}

/* Blurred backdrop */
.swal2-backdrop-show{
    backdrop-filter: blur(5px); /* Adjust blur intensity */
}

/* Icon rotation animation */
@keyframes rotateIcon {
    from {
        transform: rotateY(0deg);
    }
    to {
        transform: rotateY(360deg);
    }
}

.swal2-html-container{
    margin: 0;
    margin-top: 10px;
}

.backback{
    background-color: #347d27;
}

.noSwaps {
    display: block;
    text-align: center;
    color: rgba(107, 148, 107, 0.596);
    font-family: monospace; /* Ensures even spacing */
    white-space: pre-line; /* Keeps line breaks */
}


</style>
