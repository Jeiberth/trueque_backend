<template>
    <div>
        <profileInfo :userProfileImg="userProfile.userProfileImg"
                    :userName="userProfile.userName"
                    :owner='false'
                    :rating="userProfile.rating"
                    :things="userProfile.things"
                    :swaps="userProfile.swaps"
                    :topProfile="false"/>
        <tabChatsRates tab1="Things" tab2="Reviews">
            <template #firstTap>
                <div>
                    <thingCard
                        v-for="thing in things"
                        :key="thing.id"
                        :thing_id="thing.id"
                        :name="thing.name"
                        :description="thing.description"
                        :price="thing.price"
                        :condition="thing.condition_name"
                        :imageUrl="thing.imagesUrl"
                        :owner="false"
                        :noOwner="true"
                        :forOffer="false"
                        @seeThing="seeThing(thing)"
                    />
                </div>
            </template>
            <template #secondTap>
                <div>
                    <newRating @submitRating="submitRating" v-if="canRate"></newRating>
                    <ratingCard
                        v-for="(rating, index) in ratings"
                        :key="index"
                        :userName="rating.userName"
                        :ratingScore="rating.ratingScore"
                        :ratingComment="rating.ratingComment"
                        :ratingCreatedAt="rating.ratingCreatedAt"
                    />
                </div>
            </template>
        </tabChatsRates>
    </div>
</template>

<script setup>
    import { ref, onMounted, reactive, onBeforeUnmount } from "vue";
    import feather from "feather-icons";
    import profileInfo from "../components/profileInfo.vue";
    import thingCard from "../components/thingCard.vue";
    import tabChatsRates from "../components/tabChatsRates.vue";
    import ratingCard from "../components/ratingCard.vue";
    import newRating from "../components/newRating.vue";
    import swapApiResource from "../../api/swapResource"
    import { useRoute, useRouter } from "vue-router";
    import successErrorCard from "../components/successErrorCard.vue";
    import { useStore } from 'vuex'

    const store = useStore(); // Access Vuex store
    const swapResource = new swapApiResource();
    const router = useRouter();
    const route = useRoute();
    const userId = route.query.userId;

    const userIdAuth = store.getters.getUserId;
    const userProfile = ref({
        userProfileImg: '',
        userName: '',
        owner: false,
        rating: 0,
        things: 0,
        swaps: 0
    });
    const things = ref();
    const ratings = ref();
    const canRate = ref();

    const typeSuccessErrorCard = ref('');
    const textSuccessErrorCard = ref('');
    const showSuccessErrorCard = ref(false);

    onBeforeUnmount(() => {
        store.commit("setLoading", true);
    })

    onMounted(async () => {

        console.log("alooo");

        await swapResource
            .getUserDetails({userId: userId})
            .then((response) => {

                things.value = response.things;
                ratings.value = response.ratings;

                //v-if newRating
                canRate.value = response.rateable;

                //profile info component props
                userProfile.value.userProfileImg = response.user.profile_picture;
                userProfile.value.userName = response.user.name;
                userProfile.value.owner = response.owner;
                userProfile.value.rating = response.average_rating;
                userProfile.value.things = response.things_count;
                userProfile.value.swaps = response.swaps;

                console.log(response);
            });

        feather.replace();
        store.commit("setLoading", false);
    });

    const submitRating = async (obj) => {
        store.commit("setLoading", true);
        console.log('submitRating:', obj.comment, obj.rating);
        await swapResource
            .newRating({user_id: userIdAuth, rated_user_id: userId, score: obj.rating, comment: obj.comment})
            .then(async (response) => {
                console.log(response);
                typeSuccessErrorCard.value = 'success';
                textSuccessErrorCard.value = 'Rating Submitted Successfully';
                store.commit("setLoading", false);

                await swapResource
                    .getUserDetails({userId: userId})
                    .then((response) => {
                        ratings.value = response.ratings;
                    });

                showSuccessErrorCard.value = true;
                setTimeout(() => {
                    showSuccessErrorCard.value = false;
                }, 2800);
            });
    };

    const seeThing = (thing) => {
      console.log('seeThing:', thing);
      router.push({ name: "seeThing", query: { thing: JSON.stringify(thing) } });
    };

</script>
