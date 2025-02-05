<template>
    <div>
        <successErrorCard :type="typeSuccessErrorCard" :text="textSuccessErrorCard" :launch="showSuccessErrorCard"></successErrorCard>
        <div class="back"></div>
        <div class="container">
            <div class="cont_logo selected" id="ImageInput" @click="triggerFileInput">
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
                </div>        <h1 class="title">Edit Thing</h1>
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
                <div class="input-groupaso half-row">

                    <select id="category" v-model="selectedCategory" class="generalInput inputTotal">
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

                <div style=" display:flex; justify-content: center; align-items: center;">
                    <button type="submit" class="deleteItem" @click.prevent="deleteItem">Delete Item</button>
                    <button type="submit" class="confirmEdit" @click.prevent="confirmEdit">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
    import { ref, onMounted, onBeforeUnmount, defineComponent, reactive } from "vue";
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
    const availability = ref();

    const thing = ref({
        name: "",
        description: "",
        price: 0,
        condition_id: 0,
        weight: 0,
        color_id: "",
        material_id: 0,
        category_id: 0,
        availability: 0
    });
    
    onBeforeUnmount(() => {
        store.commit("setLoading", true);
    })

    onMounted(() => {
        //get default values from the db
        conditionArray.value = store.getters.getConditions;
        categoryArray.value = store.getters.getCategories;
        materialArray.value = store.getters.getMaterials;
        colorArray.value = store.getters.getColors;

        console.log(conditionArray.value, categoryArray.value, materialArray.value, colorArray.value);

        thing.value = JSON.parse(route.query.thing);

        //getting the values passed by querry parameters
        name.value = thing.value.name;
        description.value = thing.value.description;
        price.value = thing.value.price;
        condition.value = thing.value.condition_id;
        weight.value = thing.value.weight;
        color.value  = thing.value.color_id;
        material.value = thing.value.material_id;
        selectedCategory.value = thing.value.category_id;
        availability.value = thing.value.availability;
        //"imagesUrl"

        feather.replace();
        store.commit("setLoading", false);
    });

    const conditionArray = ref();
    const colorArray = ref();
    const materialArray = ref();
    const categoryArray = ref();

    const fileInput = ref(null);
    const images = ref([]);
    
    const triggerFileInput = () => {
        fileInput.value.click();
    };

    const handleFileUpload = (event) => {
        const files = Array.from(event.target.files);
        images.value = files;
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
    const deleteItem = async () => {
        try {
            store.commit("setLoading", true);

            const response = await swapResource.deleteThing(thing.value.id);
            if (response.success) {
                // Success response
                console.log(response);
                store.commit("setLoading", false);

                typeSuccessErrorCard.value = 'success';
                textSuccessErrorCard.value = 'Thing deleted successfully';
                showSuccessErrorCard.value = true;
                setTimeout(() => {
                    showSuccessErrorCard.value = false;
                }, 2800);
                router.push({ name: "swaps" });
            } else {
                // Error response
                console.error(response);
                store.commit("setLoading", false);

                typeSuccessErrorCard.value = 'error';
                textSuccessErrorCard.value = 'Error deleting thing';
                showSuccessErrorCard.value = true;
                setTimeout(() => {
                    showSuccessErrorCard.value = false;
                }, 2800);
            }
        } catch (error) {
            console.error(error);
            store.commit("setLoading", false);

            typeSuccessErrorCard.value = 'error';
            textSuccessErrorCard.value = 'Error deleting thing';
            showSuccessErrorCard.value = true;
            setTimeout(() => {
            showSuccessErrorCard.value = false;
            }, 2800);
        }
    }

    const confirmEdit = async () => {
        store.commit("setLoading", true);
        console.log("que  gay ve" );

        console.log({ name: name.value ,
                        description: description.value ,
                        price: price.value ,
                        condition_id: condition.value ,
                        weight: weight.value ,
                        color_id: color.value ,
                        material_id: material.value ,
                        category_id: selectedCategory.value ,
                        imagesUrl: images.value });

        
        await swapResource
            .updateThing({ thingID: thing.value.id, 
                        name: name.value ,
                        description: description.value ,
                        price: price.value ,
                        condition_id: condition.value ,
                        weight: weight.value ,
                        color_id: color.value ,
                        material_id: material.value ,
                        category_id: selectedCategory.value,
                        imagesUrl: images.value })
                        .then((response) => {
                            if (response.success) {
                                store.commit("setLoading", false);
                                // Success response
                                console.log(response);
                                typeSuccessErrorCard.value = 'success';
                                textSuccessErrorCard.value = 'Thing Successfully Edited';
                                showSuccessErrorCard.value = true;
                                setTimeout(() => {
                                    showSuccessErrorCard.value = false;
                                }, 2800);
                                router.push({ name: "swaps" })
                                
                            } else {
                            // Error response
                            console.error(response);
                            store.commit("setLoading", false);

                            typeSuccessErrorCard.value = 'error';
                            textSuccessErrorCard.value = 'Error editing thing';
                            showSuccessErrorCard.value = true;
                            setTimeout(() => {
                                showSuccessErrorCard.value = false;
                            }, 2800);
                            }
                        })
                        .catch((error) => {
                            console.error(error);
                            store.commit("setLoading", false);

                            typeSuccessErrorCard.value = 'error';
                            textSuccessErrorCard.value = 'Error editing thing';
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
    background-color: rgb(245, 255, 244);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.219);
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

    
    .selected{
        border: 1px solid #053b00; /* Add the border color */
        box-shadow: 0 0 10px rgba(5, 59, 0, 0.52); /* Create a blurry effect */
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

    .confirmEdit {
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
        margin-left: 5px;
    }

    .deleteItem{
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
        margin-right: 5px;
    }

    .switch {
      position: relative;
      display: inline-block;
      width: 60px;
      height: 34px;
    }

    .switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    .slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: #ccc;
      transition: 0.4s;
      border-radius: 34px;
    }

    .slider:before {
      position: absolute;
      content: "";
      height: 26px;
      width: 26px;
      border-radius: 50%;
      left: 4px;
      bottom: 4px;
      background-color: white;
      transition: 0.4s;
    }

    input:checked + .slider {
      background-color: #347d27;
    }

    input:checked + .slider:before {
      transform: translateX(26px);
    }

</style>
