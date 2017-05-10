/**
 * Created by lp on 10/05/2017.
 */

import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

const state = {
  variants: '',
  formAction: '',
};

const getters = {
  variants (state) {
    return state.variants;
  }
};

const mutations = {
  setVariants (state, variants) {
    state.variants = variants;
    Object.freeze(state.variants);
  },
  setFormAction(state, formAction) {
    state.formAction = formAction;
  },
};

export default new Vuex.Store({
  state,
  getters,
  mutations
})
