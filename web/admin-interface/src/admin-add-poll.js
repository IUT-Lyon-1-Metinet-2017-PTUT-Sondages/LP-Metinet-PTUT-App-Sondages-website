import Vue from 'vue'
import App from './apps/AdminAddPoll.vue'

Vue.component('page', require('./components/Page.vue'));
Vue.component('aside-page', require('./components/AsidePage.vue'));
Vue.component('question', require('./components/Question.vue'));

new Vue({
  el: '#app',
  render: h => h(App)
});
