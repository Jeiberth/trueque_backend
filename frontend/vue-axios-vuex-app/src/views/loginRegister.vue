<template>
    <div class="back"></div>
    <div class="container">
      <div class="cont_logo"></div>
      <h1 class="title">Welcome to Trueque</h1>
      <div class="form">
        <button @click="signIn" class="submit-button">Sign In</button>
        <button @click="signUp" class="submit-button">Sign Up</button>
      </div>
    </div>
</template>

<script setup>
  import { useRouter } from 'vue-router';

  const router = useRouter();

  const signUp = () => {
    console.log("signUp")
    router.push({ name: 'register' });
  };

  const signIn = () => {
    console.log("signIn")
    router.push({ name: 'login' });
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
