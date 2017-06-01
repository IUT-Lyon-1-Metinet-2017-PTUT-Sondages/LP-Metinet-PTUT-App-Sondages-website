import Vue from "vue";
import Vuex from "vuex";

Vue.use(Vuex);

const state = {
  isEditingPoll: false,
  isSubmittingPoll: false,
  poll: {},
  variants: {},
  formAction: '',
};

const getters = {
  isEditingPoll(state) {
    return state.isEditingPoll
  },
  isSubmittingPoll(state) {
    return state.isSubmittingPoll;
  },
  poll (state) {
    return state.poll;
  },
  formAction (state) {
    return state.formAction;
  },
  variants (state) {
    return state.variants;
  }
};

const mutations = {
  pollIsEditing(state) {
    state.isEditingPoll = true;
  },
  pollIsSubmitting(state) {
    state.isSubmittingPoll = true;
  },
  pollIsNotSubmitting(state) {
    state.isSubmittingPoll = false;
  },
  setPoll(state, poll) {
    poll['pages'].forEach(page => {
      page['questions'].forEach(question => {
        if (question['propositions'].length > 0) {
          question['variant'] = {...question['propositions'][0]['variant']};
        } else {
          question['variant'] = {}
        }
        question['propositions'].forEach(proposition => {
          delete proposition['variant'];
        });
      });
    });

    state.poll = {...poll};
  },
  setVariants (state, variants) {
    state.variants = {...variants};
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
