<template>
    <div class="message-container">
        <overlay v-if="loading"></overlay>
        <div class="messages">
            <div
                v-for="message in sortedMessages"
                :key="message.id"
                :class="[
                'message',
                {
                    sent: message.user_id === userIdAuth,
                    received: message.user_id !== userIdAuth,
                },
                ]"
            >
                <div class="message-content">
                    <p class="messageTextContent">{{ message.content }}</p>
                    <span class="date">{{ formatDate(message.created_at) }}</span>
                </div>
            </div>
        </div>
        <div class="input-container">
            <input
                v-model="messageToBeSend"
                type="text"
                placeholder="Type your message..."
                class="inputText"
                @keypress.enter="sendMessage"
            />
            <button @click="sendMessage" class="sendButton">
                <div class="sendButtonText">Send</div>
            </button>
        </div>
    </div>
</template>

<script setup>
    import { ref, onMounted, reactive, watch, nextTick, computed, onBeforeMount, onBeforeUnmount } from "vue";
    import feather from "feather-icons";
    import { useRoute } from 'vue-router'
    import swapApiResource from "../../api/swapResource"
    import overlay from "../components/overlay.vue";
    import { useStore } from 'vuex'
    import Echo from 'laravel-echo'

    const store = useStore();

    onBeforeUnmount(() => {
        store.commit("setLoading", true);
    })

   

    const userIdAuth = store.getters.getUserId;

    const loading = ref(false);
    const route = useRoute();  // Access the current route object
    const chatId = route.query.chatId; // Get the "id" parameter from the route
    const swapResource = new swapApiResource();

    const messageToBeSend = ref("");
    const Messages = ref([]);


    const sortedMessages = computed(() => {
        return Messages.value.slice().sort(
            (a, b) => new Date(a.created_at) - new Date(b.created_at),
        );
    });

    const sendMessage = () => {
        if (messageToBeSend.value.trim()) {
            console.log("sendMessage", messageToBeSend.value);
            swapResource
              .sendMessage({ chat_id: chatId, content:messageToBeSend.value  })
              .then((response) => {
                  console.log(response)
                  Messages.value = [...Messages.value, response];
              });
            messageToBeSend.value = ""; 
        }
    };

    const formatDate = (dateString) => {
        const date = new Date(dateString);
        return date.toLocaleString(); // Format as desired
    };


    onMounted(async () => {

        console.log(window.Echo);  // Check if Echo is properly instantiated

        window.Echo.private('chat.' + chatId)
          .listen('MessageSent', (event) => {
              if(!event.message.user_id == userIdAuth){
              Messages.value = [...Messages.value, event.message];}
              console.log("new message" , event);
          });

        // Fetch the token from Vuex or localStorage
    // const token = store.state.token || localStorage.getItem('access_token') || null;
    
    // // Check if the token is available
    // if (!token) {
    //     console.error("No token found, Echo cannot be initialized.");
    //     return;
    // }

    // console.log("Initializing Echo with token:", token);

    // // Initialize Echo dynamically with the token
    // window.Echo = new Echo({
    //     broadcaster: 'pusher',  // or 'redis' or 'socket.io'
    //     key: import.meta.env.VITE_PUSHER_APP_KEY,
    //     cluster: 'us2', // or your cluster
    //     forceTLS: true,
    //     authEndpoint: 'http://localhost:8000/broadcasting/auth',
    //     auth: {
    //         headers: {
    //             Authorization: `Bearer ${token}`,  // Set the Bearer token here
    //         },
    //     },
    // });


        await swapResource
              .getMessagesByChatId({ chat_Id: chatId })
              .then((response) => {
                  Messages.value = response;
                  console.log(Messages.value)
              });

          // Scroll to bottom on mount
          nextTick(() => {
              const container = document.querySelector(".messages");
              if (container) {
                  container.scrollTop = container.scrollHeight;
              }
          });

        store.commit("setLoading", false);
    });

//  // Watch for changes in Messages
//  watch(Messages, () => {
//         nextTick(() => {
//             const container = document.querySelector(".messages");
//             if (container) {
//                 container.scrollTop = container.scrollHeight;  // Scroll to bottom
//             }
//         });
//     });

watch(Messages, () => {
    nextTick(() => {
        const container = document.querySelector(".messages");
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    });
});


</script>

<style scoped>
/* .message-container {
    margin-top: 5px;
  height: calc(80vh - 85px); 
  overflow-y: auto;
} */

/* Hide scrollbar for Webkit browsers (Chrome, Safari, Edge) */
.message-container::-webkit-scrollbar {
    display: none; /* Hide the scrollbar */
}

/* .messages {
  display: flex;
  flex-direction: column;
  padding: 10px;

} */

.message {
  min-width: 40%;
  max-width: 70%;
  height: auto;
  border-radius: 30px;
  margin: 0;
  margin-top: 10px;
  padding: 10px;
  position: relative;
  border: 1px solid #ddd;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.219);
}

.sent {
  align-self: flex-end;
  background-color: white; /* Color for sent messages */
  border-bottom-right-radius: 0; /* No rounding on the bottom right */
}

.received {
  align-self: flex-start;
  background-color: white; /* Color for received messages */
  border-bottom-left-radius: 0; /* No rounding on the bottom left */
}

.message-content {
  display: flex;
  flex-direction: column;
}

.messageTextContent {
  margin-bottom: 0px;
  margin-left: 2px;
  margin-top: 2px;
  line-height: 1.2;
}

.date {
  font-size: 0.8em;
  color: #888;
  margin-top: 2px;
  margin-bottom: 0px;
  margin-right: 6px;
  align-self: flex-end; /* Align date to the right */
}

/* .input-container {
  display: flex;
  width: 94%;
  height: 50px;
  margin-left: 3%;
  border: 0px solid #000000;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.219);
  border-radius: 50px;
  background-color: white;
  justify-content: space-between; 
  position: absolute;
  bottom: 85px;
  z-index: 8000;
} */

.inputText {
  flex-grow: 1;
  padding: 10px;
  border-radius: 5px;
  border: transparent;
  background-color: transparent;
  margin-left: 5px;
}

.sendButton {
  position: relative;
  height: 40px;
  margin-top: 4px;
  margin-right: 5px;
  padding: 10px 15px;
  border: 0px solid #000000;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.219);
  border-radius: 50px;
  background-color:#abde97;
  color: black;
  cursor: pointer;
  align-items: center;
}

.sendButtonText {
  position: relative;
  top: -3px;
}


.message-container {
  margin-top: 10px;
  height: calc(100vh - 250px);
  position: relative;
  overflow: hidden; /* Ensure the shadow doesn't leak outside */

}

.message-container::before,
.message-container::after {
  content: ''; /* Required for pseudo-elements */
  position: absolute; /* Position over the container */
  left: 0;
  width: 100%;
  height: 50px; /* Height of the shadow area */
  pointer-events: none; /* Allow interaction with elements underneath */
  z-index: 10000; /* Ensure it sits on top of the content */
  box-shadow: inset 0 10px 10px 7px #d3ffbce8; /* White blurry shadow */
}

.message-container::before {
  top: -10px; /* Position at the top */
}

.message-container::after {
  bottom: 0; /* Position at the bottom */
  box-shadow: inset 0 -10px 10px 7px #d3ffbce8; /* Adjust shadow direction */
}

.messages {
  display: flex;
  flex-direction: column;
  padding: 10px;
  height: calc(100% - 7px); /* 70px space for input container */
  overflow-y: auto; /* Make messages scrollable */
}

/* Move the scrollbar hiding to .messages */
.messages::-webkit-scrollbar {
    display: none;
}

.input-container {
  display: flex;
  width: 94%;
  height: 50px;
  margin-left: 3%;
  border: 0px solid #000000;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.219);
  border-radius: 50px;
  background-color: white;
  justify-content: space-between;
  position: fixed; /* Keep absolute positioning */
  bottom: 85px; /* Adjust this value to position under card */
  z-index: 10000;
}


</style>
