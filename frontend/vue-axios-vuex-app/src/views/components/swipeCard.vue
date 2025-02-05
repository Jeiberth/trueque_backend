
<template>
  <div ref="tinderContainer">
    <div class="tinder--cards">
      <div
        class="tinder--card"
        v-for="(card, index) in things"
        :key="card.id"
        :style="{ display: card.swiped ? 'none' : 'block' }"
      >
        <img :src="card.imagesUrl" alt="Card image" class="card-image" />
        <div class="card-details">
          <h3 class="tittle">{{ card.name }}</h3>
          <span class="badge badge-price">~${{ card.price }}</span>
          <span class="badge badge-condition">{{ card.condition.name }}</span>
          <p class="description">{{ card.description }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, onBeforeMount } from 'vue';
import Hammer from 'hammerjs';

// Props
const props = defineProps({
  things: {
    type: Array,
    required: true,
  },
});

const emit = defineEmits(["swipeRight", "swipeLeft", "allSwiped"]);

// Refs and Reactive State
const tinderContainer = ref(null);
const things = ref([...props.things]); // Using ref to store things, a reactive version of the prop.

const handlePan = (event, el, index) => {
  el.classList.add("moving");
  const container = tinderContainer.value;
  container.classList.toggle("tinder_love", event.deltaX > 0);
  container.classList.toggle("tinder_nope", event.deltaX < 0);

  el.style.transform = `translate(${event.deltaX}px, ${event.deltaY}px) rotate(${event.deltaX * 0.03}deg)`;
};

const handlePanEnd = (event, el, index) => {
  const moveOutWidth = document.body.clientWidth;
  const isRight = event.deltaX > 0;
  console.log(things);
  try{
    const thingId = things.value[index].id;
  if(!things.value.find(thing => thing.id === thingId).swipedCalled){
      things.value.find(thing => thing.id === thingId).swipedCalled = true;
          if (Math.abs(event.deltaX) > 80) {
          const endX = isRight ? moveOutWidth : -moveOutWidth;
          el.style.transform = `translate(${endX}px, ${event.deltaY}px) rotate(${event.deltaX * 0.03}deg)`;
          el.classList.add("removed");

          setTimeout(() => {
                  things.value.find(thing => thing.id === thingId).swiped = true;
              }, 400);

          // Emit swipe event
          emit(isRight ? "swipeRight" : "swipeLeft", thingId);
          checkAllSwiped();
          } else {
          el.classList.remove("moving");
          el.style.transform = "";
          }
  }
  }catch(error){
    emit("allSwiped");
    return;
  }
};

const checkAllSwiped = () => {
  const remainingCards = things.value.filter(card => !card.swiped);
  console.log(remainingCards);
  if (remainingCards.length === 1) {
    emit("allSwiped");
  }
};

const initSwipe = () => {
  things.value.forEach((card, index) => {
    const el = document.querySelectorAll(".tinder--card")[index];
    const hammertime = new Hammer(el);

    hammertime.on("pan", (event) => handlePan(event, el, index));
    hammertime.on("panend", (event) => handlePanEnd(event, el, index));
  });
};

onMounted(() => {
  initSwipe();
});

onBeforeMount(() => {
  things.value = props.things.value;
  console.log("things.value", things.value, "otro props.things", props.things);
});

watch(things, (newThings) => {
  // Update the swipe functionality if things list changes dynamically.
  initSwipe();
});
</script>

<style scoped>
.tinder--cards {
  margin: auto;
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-top: 20px;
}

.tinder--card {
  position: absolute;
  width: 90vw;
  max-width: 400px;
  height: calc(92vh - 110px);
  overflow: hidden;
  border-radius: 30px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.219);
  transition: all 0.3s ease-in-out;
  background-color: black;
}

.card-image {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

.card-details {
  position: absolute;
  bottom: 3%;
  margin-left: 5%;
  width: 90%;
  border-radius: 30px;
  background: rgba(255, 255, 255, 0.8);
  padding: 15px;
  box-sizing: border-box;
  height: 30%;
  overflow: hidden;
}

.tittle {
  font-weight: 700;
  margin-top: 0px;
  margin-bottom: 8px;
  margin-left: 5px;
    
}

.badge {
  font-weight: 700;
  margin: 0;
  margin-right: 7px;
  margin-bottom: 5px;
  padding: 2px 10px;
  border-radius: 20px;
  color: black;
  border-width: 1px;
  border-style: solid;
}

.badge-price {
  background-color: rgba(65, 155, 95, 0.15);
  border-color: darkslategray;
}

.badge-condition {
  background-color: rgba(217, 255, 0, 0.15);
  border-color: rgb(255, 187, 0);
}

.description {
  margin-top: 10px;
  overflow: hidden;
}
</style>

