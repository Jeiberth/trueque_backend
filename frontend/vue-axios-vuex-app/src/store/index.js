import { createStore } from 'vuex';
import swapApiResource from "../api/swapResource"

const swapResource = new swapApiResource();

const store = createStore({
  state: {
    token: null,  // Store token
    userId: null,  // Store user ID
    loading: false,
    conditions: null,
    categories: null,
    materials: null,
    colors: null
  },
  mutations: {
    setToken(state, token) {
        state.token = token;
    },
    setUserId(state, userId) {
        state.userId = userId;
    },
    setLoading(state, loading) {
        state.loading = loading;
    },
    logout(state) {
        state.token = null;
        state.userId = null;
    },
    setConditions(state, conditions) {
        state.conditions = conditions;
    },
    setCategories(state, categories) {
        state.categories = categories;
    },
    setMaterials(state, materials) {
        state.materials = materials;
    },
    setColors(state, colors) {
        state.colors = colors;
    },
  },
  actions: {
    // Action to store token (typically after login)
    setAuthData({ commit }, { token, userId }) {
      commit('setToken', token);
      commit('setUserId', userId);
    },
    logout({ commit }) {
      commit('logout');
    },
    async fetchData({ commit }) {
        commit('setConditions', await swapResource.getConditions());
        commit('setCategories', await swapResource.getCategories());
        commit('setMaterials', await swapResource.getMaterials());
        commit('setColors', await swapResource.getColors());
    },
  },
  getters: {
    isAuthenticated(state) {
        return !!state.token;  // Returns true if token exists
    },
    getToken(state) {
        return state.token;
    },
    getUserId(state) {
        return state.userId;
    },
    getLoading(state) {
        return state.loading;
    },
    getConditions(state) {
        return state.conditions;
    },
    getCategories(state) {
        return state.categories;
    },
    getMaterials(state) {
        return state.materials;
    },
    getColors(state) {
        return state.colors;
    },
  }
});

export default store;
