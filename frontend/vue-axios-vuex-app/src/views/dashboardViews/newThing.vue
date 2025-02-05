<template>
    <div>
        <successErrorCard :type="typeSuccessErrorCard" :text="textSuccessErrorCard" :launch="showSuccessErrorCard"></successErrorCard>
        <div class="back"></div>
        <div class="container">
            <div class="cont_logo" id="ImageInput" @click="triggerFileInput" :class="{'selected': isFileUploaded, 'error-border': showImageError}">
                <i
                    data-feather="image"
                    class="iconStyle"
                ></i>
                    <input 
                        type="file" 
                        ref="fileInput" 
                        style="display: none" 
                        @change="handleFileUpload"
                        accept="image/*"
                        multiple
                    >
            </div>
            <h1 class="title">Add Thing</h1>
            <form @submit.prevent="handleSubmit" class="form">

            <!-- Full Row: Name -->
            <div class="input-groupaso full-row">
                <input type="text" id="name" v-model="name" required class="generalInput inputTotal" placeholder="Thing Name" />
            </div>

            <!-- Full Row: Description with scrolling -->
            <div class="input-groupaso description-row">
                <textarea id="description" v-model="description" required class="generalInput inputTotal" placeholder="Description" rows="3"></textarea>
            </div>
            <!-- Half Row: Price and Condition -->
            <div class="input-groupaso half-row">
                <input type="number" id="price" v-model="price" required class="generalInput inputHalf" placeholder="Price" />
                <select id="condition" v-model="condition" required class="generalInput inputHalf">
                    <option value="" disabled>Condition</option>
                    <option v-for="cond in conditionArray" :key="cond.id" :value="cond.id">{{ cond.name }}</option>
                </select>
            </div>

            <!-- Third Row: Weight, Color, and Material -->
            <div class="input-groupaso third-row">
                <input type="number" id="weight" v-model="weight" class="generalInput inputThird" placeholder="Weight" />
                <select id="color" v-model="color" required class="generalInput inputThird">
                    <option value="" disabled>Color</option>
                    <option v-for="colorOption in colorArray" :key="colorOption.id" :value="colorOption.id">{{ colorOption.name }}</option>
                </select>
                <select id="material" v-model="material" required class="generalInput inputThird">
                    <option value="" disabled >Material</option>
                    <option v-for="materialOption in materialArray" :key="materialOption.id" :value="materialOption.id">{{ materialOption.name }}</option>
                </select>
            </div>

            <!-- Full Row: Multi-Select Category with Tagging -->
            <div class="input-groupaso full-row">

                <select id="category" v-model="selectedCategory" required class="generalInput inputTotal">
                    <option value="" disabled >Categories</option>
                    <option v-for="cat in categoryArray" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                </select>

                <!-- <select id="category" v-model="selectedCategory" @change="addCategory" class="generalInput inputTotal">
                <option value="" disabled >Categories</option>
                <option v-for="cat in categoryArray" :key="cat.id" :value="cat.id">{{ cat.value }}</option>
                </select>

                <div class="tag-container">
                <div v-for="(cat, index) in selectedCategories" :key="cat" class="tag">
                    {{ getCategoryName(cat) }}
                    <span @click="removeCategory(cat)" class="remove-tag">&times;</span>
                </div>
                </div> -->

            </div>

            <button type="submit" class="AddThingButton">Add thing</button>
            </form>
        </div>
    </div>
</template>

<script setup>
    import { ref, onMounted, onBeforeUnmount, defineComponent, reactive, nextTick } from "vue";
    import swapApiResource from "../../api/swapResource"
    import { useStore } from 'vuex';
    import feather from "feather-icons";
    import successErrorCard from "../components/successErrorCard.vue";
    import { useRoute, useRouter } from "vue-router";

    // Using useRoute to access route params
    const route = useRoute();
    const router = useRouter();

    const typeSuccessErrorCard = ref('');
    const textSuccessErrorCard = ref('');
    const showSuccessErrorCard = ref(false);

    const store = useStore();
    const swapResource = new swapApiResource();

    const name = ref('');
    const description = ref('');
    const price = ref('');
    const condition = ref('');
    const weight = ref('');
    const color = ref('');
    const material = ref('');
    const selectedCategory = ref('');
    const selectedCategories = reactive([]);


    const conditionArray = ref([]);
    const colorArray = ref([]);
    const materialArray = ref([]);
    const categoryArray = ref([]);

    const fileInput = ref(null);
    const images = ref([]);
    const isFileUploaded = ref(false);

    const showImageError = ref(false);

    const triggerFileInput = () => {
        fileInput.value.click();
    };

    const handleFileUpload = (event) => {
        const files = Array.from(event.target.files);
        if (files.length > 0) {
            images.value = files;
            isFileUploaded.value = true;
            showImageError.value = false;
        } else {
            images.value = null;
            isFileUploaded.value = false;
        }
    };  

    onBeforeUnmount(() => {
        store.commit("setLoading", true);
    })

    onMounted(async () => {
        conditionArray.value = store.getters.getConditions;
        categoryArray.value = store.getters.getCategories;
        materialArray.value = store.getters.getMaterials;
        colorArray.value = store.getters.getColors;

        console.log(conditionArray.value, categoryArray.value, materialArray.value, colorArray.value);
        feather.replace();

        store.commit("setLoading", false);
    });


    const nullValues = () => {
        name.value = null;
        description.value = null;
        price.value = null;
        condition.value = null;
        weight.value = null;
        color.value  = null;
        material.value = null;
        selectedCategory.value = null;
        images.value = [];
    };

    const handleSubmit = async () => {

        if (!images.value || images.value.length === 0) {
            showImageError.value = true;
            textSuccessErrorCard.value = "Please upload an image";
            typeSuccessErrorCard.value = "error";
            showSuccessErrorCard.value = true;
            setTimeout(() => {
                showSuccessErrorCard.value = false;
            }, 2800);
            return; 
        }

        store.commit("setLoading", true);

        const formData = new FormData();
        
        // Append all fields
        formData.append('name', name.value);
        formData.append('description', description.value);
        formData.append('price', price.value);
        formData.append('condition_id', condition.value);
        formData.append('weight', weight.value);
        formData.append('color_id', color.value);
        formData.append('material_id', material.value);
        formData.append('category_id', selectedCategory.value);

        // Append images
        images.value.forEach((image, index) => {
            formData.append(`images[${index}]`, image);
        });

        await swapResource
        .newThing(formData)
            .then((response) => {
                nullValues();
                console.log(response);
                store.commit("setLoading", false);

                typeSuccessErrorCard.value = 'success';
                textSuccessErrorCard.value = 'Thing Succesfully Created';
                showSuccessErrorCard.value = true;
                setTimeout(() => {
                    showSuccessErrorCard.value = false;
                    router.push({ name: "profile" })
                }, 2800);
            });

        };

        
    // this is for future multi category implementations

    // const getCategoryName = (id) => {
    //     const category = categoryArray.find(cat => cat.id === id);
    //     return category ? category.value : '';
    // };

    // const addCategory = () => {
    //     if (selectedCategory.value && !selectedCategories.includes(selectedCategory.value)) {
    //         selectedCategories.push(selectedCategory.value);
    //     }
    //     selectedCategory.value = null; // Reset selection
    // };

    // const removeCategory = (id) => {
    //     const index = selectedCategories.indexOf(id);
    //     if (index !== -1) selectedCategories.splice(index, 1);
    // };


   
</script>

<style scoped>

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
    align-self: center;
    margin: auto;
    padding: 20px;
    border-radius: 50px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.219);
    top: 50vh;
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

    .form {
    display: flex;
    flex-direction: column;
    }

    .input-groupaso {
    margin-top: 20px;
    display: flex;
    flex-wrap: wrap;
    }

    .full-row {
    flex-basis: 100%;
    }

    .description-row {
    flex-basis: 100%;
    }

    .half-row {
    flex-basis: 50%;
    }

    .third-row {
    flex-basis: 33.33%;
    }

    .generalInput {
    padding: 10px;
    border: 1px solid rgb(243, 250, 241);
    background-color: rgb(243, 250, 241);
    box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.13);
    border-radius: 50px;
    padding-left: 20px;
    }

    .inputThird{
    width: 30.3%;
    margin-left: 1.5%;
    margin-right: 1.5%;
    }

    .inputTotal{
    width: 100%;
    }

    .inputHalf{
    width: 45%;
    margin-left: 2.5%;
    margin-right: 2.5%;
    }

    textarea {
    padding: 10px;
    border: 1px solid rgb(243, 250, 241);
    background-color: rgb(243, 250, 241);
    box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.13);
    border-radius: 50px;
    width: 100%;
    resize: vertical;
    overflow-y: auto;
    }

    .tag-container {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 10px;
    margin-bottom: 10px;
    }

    .tag {
    background-color: #347d27;
    color: white;
    padding: 5px 10px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    }

    .remove-tag {
    margin-left: 5px;
    cursor: pointer;
    }

    .AddThingButton {
    width: 120px;
    height: 50px;
    padding: 10px;
    border-radius: 50px;
    background-color: #347d27;
    box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.13);
    color: white;
    border: none;
    cursor: pointer;
    align-self: center;
    margin-top: 20px;
    }

</style>
