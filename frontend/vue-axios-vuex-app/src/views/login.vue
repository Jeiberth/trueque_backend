<template>
    <div class="back"></div>
    <div class="container">
    <div class="cont_logo"></div>
      <h1 class="title">Log In</h1>
      <form @submit.prevent="handleSubmit" class="form">
        <div class="input-group">
          <input type="email" id="email" v-model="email" required class="inputLogin" placeholder="E-mail"/>
        </div>
        <div class="input-group">
          <input type="password" id="password" v-model="password" required class="inputLogin" placeholder="Password"/>
          <p class="forgot" :onclick="goToRecoverPassword">Forgot your password?</p>
        </div>

        <div class="terms-container" style="margin-top: 10px;">
          <input type="checkbox" id="terms" v-model="agreedToTerms" />
          <a href="https://jeiberth.com/trueque/tspp" target="_blank" style="font-size: 12px; color: lightgray; text-decoration: none; position: relative; bottom: 3px;"> I agree to the Terms of Service and Privacy Policy</a>
        </div>

        <button type="submit" class="submit-button">Log In</button>
        <p class="createLabel" :onclick="goToRegister">Don't have an account? <strong>Create one</strong></p>
      </form>
    </div>
    <overlay v-if="loading"></overlay>
    <successErrorCard :type="type" :text="text" :launch="showSuccessErrorCard"></successErrorCard>
  </template>

  <script setup>
  import { ref, onMounted } from 'vue';
  import { useRouter } from 'vue-router';
  import swapApiResource from "../api/swapResource"
  import overlay from "./components/overlay.vue"
  import successErrorCard from './components/successErrorCard.vue';
  import { useStore } from 'vuex';

  const store = useStore(); // Access Vuex store
  const swapResource = new swapApiResource();
  const router = useRouter();

  const type = ref("error");
  const text = ref("The provided credentials are incorrect");
  const showSuccessErrorCard = ref(false);

  const loading = ref(false);
  const email = ref('');
  const password = ref('');
  const agreedToTerms = ref(false);  


  onMounted(() => {
      store.commit("setLoading", false);
  })

  const goToRegister = () => {
    console.log("goToRegister")
    router.push({ name: 'register' });
  };

  const goToRecoverPassword = () => {
    console.log("goToRecoverPassword")
    router.push({ name: 'emailPasswordReset' });
  };

  const handleSubmit = () => {
    if (!agreedToTerms.value) {
        return;
    }

    loading.value = true;
    console.log('Email:', email.value);
    console.log('Password:', password.value);
    swapResource
      .login({ email: email.value, password: password.value }) // Adjust API parameters as needed
      .then((response) => {
        if ('token' in response) {
            console.log(response);
            store.dispatch('setAuthData', { token: response.token , userId: response.user_id });
            localStorage.setItem('access_token', response.token);
            router.push({ name: 'swaps' });
        }else if('verify' in response){
          type.value = "info";
          text.value = response.message;
          showSuccessErrorCard.value = true;
          setTimeout(() => {
              showSuccessErrorCard.value = false;
          }, 2800);
          router.push({ name: 'verifyEmail'});
        }else{
            console.log(response);
            showSuccessErrorCard.value = true;
            setTimeout(() => {
                showSuccessErrorCard.value = false;
            }, 2800);
        }
      })
      .catch((error) => {
        text.value = "Email or password are incorrect.";
        showSuccessErrorCard.value = true;
        setTimeout(() => {
            showSuccessErrorCard.value = false;
        }, 2800);
        console.error('Login failed:', error);
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


    
  </style>
