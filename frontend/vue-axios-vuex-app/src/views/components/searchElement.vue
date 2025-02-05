<template>
  <div>
      <div class="input-container">
          <input
          v-model="filters.searchTerm"
          type="text"
          placeholder=" Search..."
          class="inputText"
          @click="showFiltersMethod"
          @keypress.enter="searchThings"
          />
          <button @click="searchThings" class="sendButton">
              <div class="sendButtonText"><strong>Search</strong></div>
          </button>
      </div>

      <!-- Filter options container -->
      <div class="filter-options" v-if="showFilters">
          <!-- Price filter -->
          <div class="card selectMultiple">
            $
            <input
                type="number"
                v-model="filters.price"
                placeholder=" Max Price"
                class="priceInput"
            />
          </div>

           <!-- Weight filter -->
          <div class="card selectMultiple displFlex">
            <input
                type="number"
                v-model="filters.range"
                placeholder="Range"
                class="rangeInput"
            />Km</div>

          <!-- Condition filter -->
          <select v-model="filters.condition" class="card">
          <option value="" disabled selected>Condition</option>
          <option
              v-for="condition in conditions"
              :key="condition.id"
              :value="condition.id"
          >
              {{ condition.name }}
          </option>
          </select>

          <!-- Weight filter -->
          <div class="card selectMultiple displFlex">
          <input
              type="number"
              v-model="filters.weight"
              placeholder="MaxWeight"
              class="weightInput"
          />Kg
          </div>

          <!-- <div >
              <div class="card selectMultiple" ref="dropdown" style="margin-left: 8px">
                  <div @click="showCategories = !showCategories">
                      Category <strong class="arrowDown">v</strong>
                  </div>
                  <div class="optionsContainer" v-if="showCategories">
                      <div class="optionsCatg" v-for="category in categories">
                          {{ category.name }}
                          <input
                          type="checkbox"
                          :id="category.id"
                          :name="category.name"
                          :value="category.name"
                          :checked="isChecked(category)"
                          @change="updateCategories(category, $event.target.checked)"
                          />
                      </div>
                  </div>
              </div>
          </div> -->

           <!-- Material filter -->
          <select v-model="filters.category" class="card">
            <option value="" disabled selected>Category</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
            </option>
          </select>

          <!-- Material filter -->
          <select v-model="filters.material" class="card">
          <option value="" disabled selected>Material</option>
          <option v-for="material in materials" :key="material.id" :value="material.id">
              {{ material.name }}
          </option>
          </select>

          <!-- Color filter -->
          <select v-model="filters.color" class="card">
          <option value="" disabled selected>Color</option>
          <option v-for="color in colors" :key="color.id" :value="color.id">
              {{ color.name }}
          </option>
          </select>
      </div>
  </div>
</template>

<script>
import { ref, onMounted, onBeforeUnmount, defineComponent } from "vue";
import swapApiResource from "../../api/swapResource"
import { useStore } from 'vuex';


export default defineComponent({
name: 'searchelement',
emits: ['searchThingsEmit', 'showFiltersEmit'],
setup(props, { emit }) {

  
  const store = useStore();

  const filters = ref({
    searchTerm: "",
    price: "",
    condition: "",
    weight: "",
    category: "",
    material: "",
    color: "",
  });
  const dropdown = ref(null);
  const showFilters = ref(false);

  // Change these to ref so that they are correctly accessed
  const conditions = ref([]);
  const categories = ref([]);
  const materials = ref([]);
  const colors = ref([]);
  const showCategories = ref(false);

  const swapResource = new swapApiResource();

  const isChecked = (category) => {
    return filters.value.category.includes(category);
  };

  const updateCategories = (category, isChecked) => {
    if (isChecked) {
      filters.value.category.push(category);
    } else {
      const index = filters.value.category.indexOf(category);
      if (index > -1) {
        filters.value.category.splice(index, 1);
      }
    }
  };

  const searchThings = () => {
    showFilters.value = false;
    console.log(filters.value);
    emit('searchThingsEmit', filters.value);
    filters.value = {
      searchTerm: "",
      price: "",
      condition: "",
      weight: "",
      category: "",
      range: "",
      material: "",
      color: "",
    };
  };

  const showFiltersMethod = () => {
    showFilters.value = true;
    console.log('showFiltersEmit');
    emit('showFiltersEmit');
  };

  const handleClickOutside = (event) => {
    if (dropdown.value && !dropdown.value.contains(event.target)) {
      showCategories.value = false;
    }
  };

  onMounted(async () => {
    document.addEventListener("click", handleClickOutside);

    // Use ref values properly
    conditions.value = store.getters.getConditions;
    categories.value = store.getters.getCategories;
    materials.value = store.getters.getMaterials;
    colors.value = store.getters.getColors;

    console.log(conditions.value, categories.value, materials.value, colors.value);
  });

  onBeforeUnmount(() => {
    document.removeEventListener("click", handleClickOutside);
  });

  return {
    filters,
    conditions,
    categories,
    materials,
    colors,
    showCategories,
    isChecked,
    updateCategories,
    searchThings,
    handleClickOutside,
    dropdown,
    showFilters,
    showFiltersMethod
  };
}
});

</script>

<style scoped>
*{
  box-sizing: border-box;
}

.input-container {
display: flex;
width: 94%;
height: 40px;
margin-left: 3%;
margin-bottom: 10px;
margin-top: 10px;
border: 0px solid #000000;
box-shadow: 0 4px 15px rgba(0, 0, 0, 0.219);
border-radius: 50px;
background-color: white;
justify-content: space-between; /* Space between input and button */
}

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
height: 30px;
margin-top: 4px;
margin-right: 5px;
padding: 10px 15px;
border: 0px solid #000000;
box-shadow: 0 4px 15px rgba(0, 0, 0, 0.219);
border-radius: 50px;
background-color: #abde97;
color: black;
cursor: pointer;
align-items: center;
}

.sendButtonText {
position: relative;
font-size: small;
top: -3px;
}

.card {
border: 1px solid #ddd;
border-radius: 5px;
padding: 10px;
box-shadow: 0 4px 15px rgba(0, 0, 0, 0.219);
margin-left: 2%;
border-radius: 50px;
height: 40px;
min-width: 120px;
max-width: 120px;
background-color: white;
}

.searchKeyword {
width: 94%;
margin-left: 3%;
margin-bottom: 10px;
}

.filter-input {
position: relative;
}

.filter-options {
display: flex;
flex-wrap: nowrap;
overflow-x: scroll;
border: 1px transparent #ccc;
background: transparent;
padding: 5px;
z-index: 10;
width: 100%; /* Full width */
height: 100vh;
}

select {
padding-top: 8px !important;
padding-bottom: 12px !important;
}

.selectMultiple {
padding-top: 8px !important;
padding-bottom: 12px !important;

}

.optionsContainer {
position: relative;
border: 1px solid#ccc;
background-color: white;
top: 13px;
width: 140px;
left: -10px;
border-radius: 20px;
padding-top: 0px;
padding-bottom: 12px;
box-shadow: 0 4px 15px rgba(0, 0, 0, 0.219);
}

.optionsCatg {
margin-top: 10px;
margin-left: 10px;
}

.priceInput {
position: relative;
top: 0px;
border: transparent;
left: 10px;
width: 80px;
}

.rangeInput {
position: relative;
top: 2px;
border: transparent;
left: 0px;
width: 76px;
}

.weightInput {
position: relative;
top: 2px;
border: transparent;
left: 0px;
width: 76px;
}

.displFlex {
display: flex;
flex-direction: row;
}

.arrowDown {
display: inline-block;
position: relative;
left: 20px;
transform: scaleY(0.6);
}
</style>
