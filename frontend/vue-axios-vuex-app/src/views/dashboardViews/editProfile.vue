<template>
    <div style="overflow: hidden;">
        <div class="back"></div>
        <div class="container">
        <form @submit.prevent="handleSubmit" class="form">
        <div class="cont_logo selected" id="ImageInput" @click="triggerFileInput" >
            <i data-feather="image" class="iconStyle" >
                <input 
                    type="file" 
                    ref="fileInput" 
                    style="display: none" 
                    required
                    @change="handleFileUpload"
                    accept="image/*"
                    multiple
                >
            </i>
        </div>
        <h1 class="title">Edit Profile</h1>

            <div class="input-group">
            <input type="text" id="name" v-model="name" required class="inputLogin" placeholder="Full Name"/>
            </div>
            <div class="input-group">
              <input type="text" id="city" v-model="address" @input="onInputChange" :disabled="isSearching" required class="inputLogin" placeholder="Enter your address"/>
              <div v-if="suggestions.length" class="dropdown" >
                <div v-for="(suggestion, index) in suggestions" :key="index" class="dropdown-item" @click="selectAddress(suggestion)">
                  {{ suggestion.display_name }}
                </div>
              </div>
            </div>
            <div class="input-group">
            <input type="email" id="email" v-model="email" required class="inputLogin" placeholder="E-mail"/>
            </div>
            <button type="submit" class="submit-button">Confirm</button>
            <p class="createLabel" @click="goToReset">Reset password</p>
        </form>
        </div>
        <overlay v-if="loading"></overlay>
        <successErrorCard :type="type" :text="text" :launch="showSuccessErrorCard"></successErrorCard>
    </div>
  </template>

<style scoped>
.dropdown {
  position: absolute;
  background-color: white;
  border-radius: 5px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-height: 150px;
  overflow-y: auto;
  z-index: 1000;
}
.dropdown-item {
  padding: 8px;
  cursor: pointer;
  font-size: 16px;
  transition: background 0.2s;
}
.dropdown-item:hover {
  background-color: #f0f0f0;
}
</style>


  <script setup>
     import { ref, onMounted, reactive, onBeforeUnmount } from "vue";
  import { useRouter } from 'vue-router';
  import swapApiResource from "../../api/swapResource"
  import overlay from "../components/overlay.vue"
  import successErrorCard from '../components/successErrorCard.vue';
  import { useStore } from 'vuex'
  import axios from 'axios';

  const store = useStore(); // Access Vuex store 
  const swapResource = new swapApiResource();
  const router = useRouter();

  const type = ref("success");
  const text = ref("");
  const showSuccessErrorCard = ref(false);
  const userIdAuth = store.getters.getUserId;

  const loading = ref(false);

  const name = ref('');
  const city = ref('');
  const email = ref('');
  const password = ref('');

  const citiesArray = reactive(['Alice', 'Bob', 'Charlie', 'Diana']);

  const fileInput = ref(null);
  const profileImg = ref();
  const isFileUploaded = ref(false);

  const showImageError = ref(false);


  const suggestions = ref([]);
  const address = ref('');
  const lat = ref(null);
const lon = ref(null);

  const onInputChange = () => {
    clearTimeout(typingTimeout);
    typingTimeout = setTimeout(fetchAddressSuggestions, 500); // Fetch suggestions after user stops typing for 500ms
  };

  const isSearching = ref(false);

  const fetchAddressSuggestions = async () => {
    if (address.value.length < 3) return;

    isSearching.value = true;

    try {
        const response = await axios.get(`https://photon.komoot.io/api/`, {
            params: {
                q: address.value,
                limit: 5
            }
        });
        suggestions.value = response.data.features.map(feature => ({
          display_name: (feature.properties.housenumber && feature.properties.street)
            ? `${feature.properties.housenumber} ${feature.properties.street}, ${feature.properties.city}`
            : `${feature.properties.name}, ${feature.properties.city}`,
          lat: feature.geometry.coordinates[1],
          lon: feature.geometry.coordinates[0]
        }));

    } catch (error) {
        console.error("Error fetching address suggestions", error);
    }

    isSearching.value = false;
  };

  let typingTimeout = null;

  const selectAddress = (suggestion) => {
    address.value = suggestion.display_name;
    lat.value = suggestion.lat;
    lon.value = suggestion.lon;
    suggestions.value = [];
  };


  onBeforeUnmount(() => {
        store.commit("setLoading", true);
    })

    onMounted(async () => {

        await swapResource
            .getUserDetails({userId: userIdAuth})
            .then((response) => {

                name.value = response.user.name;
                address.value = response.user.location;
                email.value = response.user.email;

                console.log("perro" ,name, email, city);
            });
        
        store.commit("setLoading", false);

    });


  const triggerFileInput = () => {
    fileInput.value.click();
  };

  const handleFileUpload = (event) => {
    const files = Array.from(event.target.files);
    if (files.length > 0) {
      profileImg.value = files;
      isFileUploaded.value = true;
      showImageError.value = false;
    } else {
      profileImg.value = null;
      isFileUploaded.value = false;
    }
  };  

  const goToReset = () => {
    console.log("goToReset");
    router.push({ name: 'resetPassword' });
  };

  const handleSubmit = async () => {
    store.commit("setLoading", true);
  
    await swapResource
      .updateUser({ name: name.value, 
                    address: {
                        street: address.value,
                        latitude: lat.value,
                        longitude: lon.value,
                    }, email: email.value, profileImg: profileImg.value }) // Adjust API parameters as needed
      .then((response) => {
            if (response.success) {
                store.commit("setLoading", false);
                // Success response
                console.log(response);
                type.value = 'success';
                text.value = 'User Successfully Updated';
                showSuccessErrorCard.value = true;
                setTimeout(() => {
                    showSuccessErrorCard.value = false;
                }, 2800);
                router.push({ name: "swaps" })
                
            } else {
                // Error response
                console.error(response);
                store.commit("setLoading", false);

                type.value = 'error';
                text.value = 'Error Updating User';
                showSuccessErrorCard.value = true;
                setTimeout(() => {
                    showSuccessErrorCard.value = false;
                }, 2800);
            }
        })
        .catch((error) => {
            console.error(error);
            store.commit("setLoading", false);

            type.value = 'error';
            text.value = 'Error Updating User';
            showSuccessErrorCard.value = true;
            setTimeout(() => {
            showSuccessErrorCard.value = false;
            }, 2800);
        });

  };
  </script>

  <style scoped>
  .back {
    position: fixed;
    top: 0;
    left: 0;
    background-color: #d3ffbc;
    width: 100%;
    height: 100%;
  }

  .container {
    margin-left: 3%;
    width: 94%;
    position: relative;
    background-color: white;
    height: auto;
    margin-top: 50vh;
    padding: 20px;
    border-radius: 50px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.219);
    transform: translateY(-50%);
  }

  .cont_logo {
    position: absolute;
    top: -50px;
    left: 50%;
    transform: translateX(-50%);
    width: 100px;
    height: 100px;
    border-radius: 50px;
    border: 1px solid transparent; /* Initially transparent border */
    background-color: rgb(245, 255, 244);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.219);
    transition: all 2s ease;
  }

    .selected{
      border: 1px solid #053b00; /* Add the border color */
      box-shadow: 0 0 10px rgba(5, 59, 0, 0.52); /* Create a blurry effect */
    }

    .iconStyle{
    position: relative;
    top: 20px;
    left: 20px;
    width: 60px;
    height: 60px;
    color: rgb(224, 224, 224);
    }

  .title {
    text-align: center;
    font-size: xx-large;
    margin-top: 50px;
  }

  .error-border {
    border: 2px solid #ff4444 !important;
    animation: shake 0.5s ease-in-out;
  }

  @keyframes shake {
    0% { transform:  translateX(-50%) }
    25% { transform: translateX(-55%); }
    50% { transform: translateX(-50%); }
    75% { transform: translateX(-55%); }
    100% { transform:  translateX(-50%) }
  }


  .form {
    display: flex;
    flex-direction: column;
  }

  .input-group {
    margin-top: 20px;
  }

  .labelInput {
    margin-bottom: 3px;
    font-weight: 600;
    margin-left: 10px;
    color: black;
    display: none;
  }

  .inputLogin {
    padding: 10px;
    border: 1px solid rgb(243, 250, 241) !important;
    background-color: rgb(243, 250, 241);
    box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.13) !important;
    border-radius: 50px !important;
    width: 100%;
    padding-left: 20px !important;
  }

  .submit-button {
    width: 75px;
    margin: auto;
    padding: 10px;
    border-radius: 50px;
    background-color: #347d27;
    box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.13) !important;
    color: white;
    border: none;
    cursor: pointer;
    margin-top: 20px;
    margin-bottom: 10px;
  }

  .createLabel {
    margin: auto;
    margin-top: 10px;
    position: relative;
    bottom: 0px;
    opacity: 0.3;
    font-size: small;
  }
  </style>
