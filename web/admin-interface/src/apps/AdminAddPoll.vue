<template>
  <form class="form-horizontal container-fluid" method="post" :action="formAction">
    <input v-if="isEditingPoll" :value="poll.id" type="hidden" name="poll[id]">

    <h1 class="text-center">
      {{ isEditingPoll ? $t('poll.updating') : $t('poll.creation') }}
    </h1>
    <hr>

    <!-- Titre du sondage -->
    <div class="form-group" :class="{'has-danger': poll.title.length == 0}">
      <input v-model="poll.title" name="poll[title]" required
             class="form-control form-control-lg" :placeholder="$t('poll.placeholder.title')">
    </div>

    <!-- Description du sondage -->
    <div class="form-group">
            <textarea v-model="poll.description" name="poll[description]"
                      class="form-control" :placeholder="$t('poll.placeholder.description')"></textarea>
    </div>

    <!-- Les pages sondage, avec une transion fade -->
    <hr>
    <transition-group name="fade" tag="div">
      <page v-for="page, pageIndex in poll.pages" :key="page"
            :poll="poll"
            :page="page" :pageIndex="pageIndex"></page>
    </transition-group>
    <hr>

    <div class="text-center">
      <button type="submit" class="btn btn-primary btn-lg">
        {{ isEditingPoll ? $t('poll.update') : $t('poll.create') }}
      </button>
    </div>
  </form>
</template>

<script>
  import Bus from '../bus/admin-add-poll';
  import * as Event from '../bus/events';
  import {mapGetters} from "vuex";

  export default {
    computed: {
      ...mapGetters(['isEditingPoll', 'poll', 'variants', 'formAction'])
    },
    methods: {
      addPageBefore(index = 0) {
        this._addPage(index);
      },
      addPageAfter(index = 0) {
        this._addPage(index, false);
      },
      removePage (page) {
        const index = this.poll.pages.indexOf(page);
        this.poll.pages.splice(index, 1);
      },
      addQuestionBefore(page, questionIndex = 0) {
        this._addQuestion(page, questionIndex);
      },
      addQuestionAfter(page, questionIndex = 0) {
        this._addQuestion(page, questionIndex, false);
      },
      removeQuestion(page, questionIndex) {
        page.questions.splice(questionIndex, 1);
      },
      _addPage(pageIndex = 0, before = true) {
        // si !before, on incrémente le pageIndex pour ajouter la page après
        pageIndex += (before ? 0 : 1);

        // Ajout de la page
        this.poll.pages.splice(pageIndex, 0, {
          title: this.$t('page.default.title'),
          description: this.$t('page.default.description'),
          questions: []
        });

        // Puis on y rajoute une question
        this.addQuestionBefore(this.poll.pages[pageIndex]);
      },
      _addQuestion(page, questionIndex = 0, before = true) {
        questionIndex += (before ? 0 : 1);

        page.questions.splice(questionIndex, 0, {
          title: this.$t('question.default.title'),
          variant: {
            name: this.variants[Object.keys(this.variants)[0]] // premier élément d'un objet
          },
          propositions: [
            {title: ''},
          ]
        });
      }
    },
    created () { // Cycle de vie VueJS: 1er hook dispo après l'instanciation de l'app VueJS
      Bus.$on(Event.ADD_PAGE_BEFORE, this.addPageBefore);
      Bus.$on(Event.ADD_PAGE_AFTER, this.addPageAfter);
      Bus.$on(Event.REMOVE_PAGE, this.removePage);

      Bus.$on(Event.ADD_QUESTION_BEFORE, this.addQuestionBefore);
      Bus.$on(Event.ADD_QUESTION_AFTER, this.addQuestionAfter);
      Bus.$on(Event.REMOVE_QUESTION, this.removeQuestion);

      // Ajout dans le store...
      'VARIANTS' in window && this.$store.commit('setVariants', window['VARIANTS']);
      'FORM_ACTION' in window && this.$store.commit('setFormAction', window['FORM_ACTION']);
      'IS_EDITING_POLL' in window && this.$store.commit('pollIsEditing');

      // Doit être fait après les 3 ajouts dans le store ci-dessus
      if ('POLL' in window) {
        this.$store.commit('setPoll', window['POLL']);
      } else {
        this.$store.commit('setPoll', { // L'objet qui va contenir les pages, les questions, et les propositions
          title: this.$t('poll.default.title'), // this.$t = fonction rajoutée par VueI18n
          description: this.$t('poll.default.description'),
          pages: []
        });

        this.addPageBefore();
      }
    },
    mounted () {
    }
  }
</script>

<style scoped>
  form {
    max-width: 1024px;
  }
</style>

<style>
  .fade-enter-active, .fade-leave-active {
    transition: opacity .2s
  }

  .fade-enter, .fade-leave-to {
    opacity: 0
  }
</style>
