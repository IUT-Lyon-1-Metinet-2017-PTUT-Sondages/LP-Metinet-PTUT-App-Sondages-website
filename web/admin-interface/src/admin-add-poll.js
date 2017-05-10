import Vue from "vue";
import VueI18n from "vue-i18n";
import App from "./apps/AdminAddPoll.vue";
import store from './store';

Vue.use(VueI18n);

// On configure VueI18n avec les variables JavaScript globales générées via Symfony
Vue.config.lang = window['LOCALE'] || 'fr';
Vue.locale(Vue.config.lang, Object.freeze( window['TRANSLATIONS'] || {}));

// Les composants <page>, <question>, ...
Vue.component('page', require('./components/Page.vue'));
Vue.component('question', require('./components/Question.vue'));
Vue.component('propositionsRadio', require('./components/PropositionsRadio.vue'));
Vue.component('propositionsCheckbox', require('./components/PropositionsCheckbox.vue'));
Vue.component('propositionsLinearScale', require('./components/PropositionsLinearScale.vue'));

// On lance l'application
new Vue({
  store,
  el: '#app',
  render: h => h(App)
});
