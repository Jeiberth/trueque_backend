<template>
    <div class="back"></div>
    <div class="container">
    <div class="cont_logo"></div>
      <h2 class="title">Enter Your Email for Password Reset</h2>
      <form @submit.prevent="handleSubmit" class="form">
        <div class="input-group">
          <input type="code" id="code" v-model="code" required class="inputLogin" placeholder="Your Email"/>
        </div>
        <button type="submit" class="submit-button">Submit</button>
        <!-- <p class="createLabel" :onclick="goToRegister">Don't have an account? <strong>Create on</strong></p> -->
      </form>
    </div>
    <overlay v-if="loading"></overlay>
    <successErrorCard :type="type" :text="text" :launch="showSuccessErrorCard"></successErrorCard>
  </template>

  <script setup>
  import { ref } from 'vue';
  import { useRouter, useRoute } from 'vue-router';
  import swapApiResource from "../api/swapResource"
  import overlay from "./components/overlay.vue"
  import successErrorCard from './components/successErrorCard.vue';
  import { useStore } from 'vuex';

  const store = useStore(); // Access Vuex store
  const swapResource = new swapApiResource();
  const router = useRouter();
  const route = useRoute();

  const loading = ref(false);
  const type = ref("error");
  const text = ref("The Email doesn't exist");
  const showSuccessErrorCard = ref(false);
  const code = ref('');

  const handleSubmit = () => {
    console.log("code.value" , code.value)
    loading.value = true;
    swapResource
      .resetPasswordSendVerCode(code.value) // Adjust API parameters as needed
      .then((response) => {
        if (response.success) {
            console.log(response);
            type.value = "success";
            text.value = "Check your email!";
            showSuccessErrorCard.value = true;
            setTimeout(() => {
                showSuccessErrorCard.value = false;
            }, 2800);
            router.push({ name: 'resetPassword' });
        }else{
            console.log(response);
            text.value = "The Email doesn't exist";
            showSuccessErrorCard.value = true;
            setTimeout(() => {
                showSuccessErrorCard.value = false;
            }, 2800);
        }
      })
      .catch((error) => {
        text.value = error.message;
        showSuccessErrorCard.value = true;
        setTimeout(() => {
            showSuccessErrorCard.value = false;
        }, 2800);
        console.error('Email failed:', error);
      })
      .finally(() => {
        loading.value = false; // Reset loading state
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

  .cont_logo{
    position: absolute;
    top: -50px;
    left: 50%;
    transform: translateX(-50%);
    width: 100px;
    height: 100px;
    border-radius: 50px;
    background-color: black;
  }

  .title {
    text-align: center;
    font-size: xx-large;
    margin-top: 50px;
    margin-bottom: 0px;
  }

  .form {
    display: flex;
    flex-direction: column;
  }

  .input-group {
    margin-top: 20px;
    margin-bottom: 20px;
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

  .forgot{
    width: 100%;
    opacity: 0.3;
    margin-top: 10px;
    font-size: small;
    text-align: right;
    margin-bottom: 0;
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
    margin-top: 5px;
    margin-bottom:10px;
}

  .createLabel{
    margin: auto;
    margin-top: 10px;
    position: relative;
    bottom: 0px;
    opacity: 0.3;
    font-size: small;
    }

    #code{
        width: 90%;
        margin-left: 50%;
        transform: translateX(-50%);
    }
  </style>
