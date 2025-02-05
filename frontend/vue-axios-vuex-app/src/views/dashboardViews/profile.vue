<template>
    <div>
        <profileInfo :userProfileImg="userProfile.userProfileImg"
                    :userName="userProfile.userName"
                    :owner='true'
                    :rating="userProfile.rating"
                    :things="userProfile.things"
                    :swaps="userProfile.swaps"
                    @addThing="addThing()"
                    @editUser="editUser()"
                    @editProfile="editProfile()"/>
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
                        :owner="true"
                        :noOwner="false"
                        :forOffer="false"
                        @editThing="handleEdit(thing)"
                    />
                </div>
            </template>
            <template #secondTap>
                <div>
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
    import swapApiResource from "../../api/swapResource"
    import { useRoute, useRouter } from "vue-router";
        import { useStore } from 'vuex'

    const store = useStore(); // Access Vuex store
    const swapResource = new swapApiResource();
    const router = useRouter();
    const route = useRoute();

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

    onBeforeUnmount(() => {
        store.commit("setLoading", true);
    })

    onMounted(async () => {

        await swapResource
            .getUserDetails({userId: userIdAuth})
            .then((response) => {

                things.value = response.things;
                ratings.value = response.ratings;

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

    const handleEdit = (thing) => {
      console.log('Edit thing:', thing);
      router.push({ name: "editThing", query: { thing: JSON.stringify(thing) } });
    };

    const addThing = () => {
      console.log("addThing");
      router.push({ name: "newthing" });
    };

    const editProfile = () => {
      console.log("editProfile");
      router.push({ name: "editProfile" });
    };

    const editUser = async () => {
      console.log("EditUser");
      store.commit("setLoading", true);
      await swapResource
            .logout()
            .then((response) => {
                console.log(response);
        });
        store.dispatch('logout');
      router.push({ name: 'login' });
    }

</script>
