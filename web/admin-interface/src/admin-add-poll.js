import Vue from 'vue'
import VueI18n from 'vue-i18n'
import App from './apps/AdminAddPoll.vue'

Vue.use(VueI18n);
Vue.config.lang = window['LOCALE'] || 'fr';

Vue.component('page', require('./components/Page.vue'));
Vue.component('question', require('./components/Question.vue'));
Vue.component('propositionsRadio', require('./components/PropositionsRadio.vue'));
Vue.component('propositionsCheckbox', require('./components/PropositionsCheckbox.vue'));
Vue.component('propositionsLinearScale', require('./components/PropositionsLinearScale.vue'));

new Vue({
  el: '#app',
  render: h => h(App)
});
