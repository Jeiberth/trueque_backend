<template>
    <div class="card" @click="selectChat">
        <div class="card-image">
            <img :src="image" alt="Description" class="card-image-img">
        </div>
        <div class="card-body">
            <div class="user-info">
                <h5 class="userName" style="white-space: nowrap; overflow-x: hidden; text-overflow: ellipsis;">{{ userName }}</h5>
                <h6 class="user-info-notif off" v-if="offer > 1">{{ offer }} New Offers</h6>
                <h6 class="user-info-notif off" v-if="offer == 1">1 New Offer</h6>
                <h6 class="user-info-notif mess" v-if="message == 1">1 New Message</h6>
                <h6 class="user-info-notif mess" v-if="message > 1">{{ message }} New Messages</h6>
            </div>
        </div>
        <div class="card-circle" v-if="offer > 0 || message > 0">
            <span class="offer-badgee">{{ offer + message }}</span>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    chatId: {
        type: Number,
    },
    userName: {
        type: String,
        default: 'Username',
    },
    image: {
        type: String,
        default: 'src/assets/images/p2.jpg',
    },
    message: {
        type: Number,
        default: 0,
    },
    offer: {
        type: Number,
        default: 0,
    },
});

// Emits
const emit = defineEmits(['selectChat']);

// Emit functions
const selectChat = () => {
    emit('selectChat');
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
}

.userName {
    margin: 4px;
    font-weight: 600;
    font-size:larger;
    padding-bottom: 0px;
    margin-bottom: 0px;
    height: 30px;
}

.user-info-notif {
    font-size: small;
    margin: 0;
    margin-bottom: 5px;
    height: 22px; /* This can stay, but make sure it's the height you want */
    padding: 0 5px;
    border-radius: 50px;
    color: black;
    border: 1px solid black; /* Combine border properties */

    display: flex; /* Use flexbox for alignment */
    justify-content: left; /* Center horizontally */
    align-items: center; /* Center vertically */
}


.mess {
    background-color: rgba(65, 155, 95, 0.15);
    border-color: darkslategray;
}

.off {
    background-color: rgba(217, 255, 0, 0.15);
    border-color: rgb(255, 187, 0);
}

.card-circle {
    margin-right: 10px; /* Add margin to the left */
    width: 25px;
    height: 25px;
    background-color: black;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
}

span {
    color: white;
    margin: 0;
    font-weight: 600;
}
</style>
