<template>
    <div>
        <overlay v-if="loading"></overlay>
        <cardForClick text="Make an Offer" icon="plus-circle" @clicked="makeAnOffer"></cardForClick>
        <offerCard @openOffer="openOffer(offer.id)"
                    v-for="(offer, index) in offers"
                    :offerStatus="offer.status_id"
                    :offerSend="offer.offerSend"
                    :created_at="offer.created_at"
                    :offerId="offer.id">
        </offerCard>
    </div>
</template>

<script setup>
    import overlay from "../components/overlay.vue";
    import cardForClick from "../components/cardForClick.vue";
    import offerCard from "../components/offerCard.vue";
    import { ref, onMounted, reactive , onBeforeUnmount} from "vue";
    import feather from "feather-icons";
    import swapApiResource from "../../api/swapResource"
    import { useRoute, useRouter } from "vue-router";
    import { useStore } from 'vuex'

    const store = useStore();

    // Using useRoute to access route params
    const route = useRoute();
    const router = useRouter();

    const loading = ref(false);
    const userId = route.query.userId;
    const chatId = route.query.chatId;
    const swapResource = new swapApiResource();


    onBeforeUnmount(() => {
        store.commit("setLoading", true);
    })

    onMounted(async () => {
        feather.replace();

        //Look at offers
        console.log("useRIOD", userId)
        await swapResource
            .getOffersBetweenUsers({ userId: userId })
            .then((response) => {
                offers.value = response;
                console.log(offers.value);
            });

        store.commit("setLoading", false);
    });

    const offers = ref([]);

    const openOffer = (offerId) => {
        console.log("watchOffer", offerId);
        router.push({ name: "watchOffer", query: { offerId: offerId } });
    }

    const makeAnOffer = () => {
        console.log("makeAnOffer");
        router.push({ name: "makeOffer", query: { userId: userId, chatId: chatId } });
    }

</script>
