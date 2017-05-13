<template>
  <form @submit.prevent="onSubmit" class="form-horizontal container-fluid" method="post">
    <input v-if="isEditingPoll && 'id' in poll" :value="poll.id" type="hidden" name="poll[id]">
    <template v-if="isEditingPoll">
      <input v-for="pageId in toDelete.pages" name="toDelete['Page'][]" :value="pageId" type="hidden">
      <input v-for="questionId in toDelete.questions" name="toDelete['Question'][]" :value="questionId" type="hidden">
      <input v-for="propositionId in toDelete.propositions" name="toDelete['Proposition'][]" :value="propositionId"
             type="hidden">
    </template>

    <h1 class="text-center">
      {{ isEditingPoll ? $t('poll.updating') : $t('poll.creation') }}
    </h1>
    <hr>

    <!-- Titre du sondage -->
    <div class="form-group" :class="{'has-danger': poll.title.error}">
      <input v-model="poll.title.value" name="poll[title]"
             :placeholder="$t('poll.placeholder.title')"
             class="form-control form-control-lg">
      <div v-if="poll.title.error" class="form-control-feedback">{{ poll.title.error }}</div>
    </div>

    <!-- Description du sondage -->
    <div class="form-group" :class="{'has-danger': poll.description.error}">
      <textarea v-model="poll.description.value"
                :placeholder="$t('poll.placeholder.description')"
                name="poll[description]" class="form-control"></textarea>
      <div v-if="poll.description.error" class="form-control-feedback">{{ poll.description.error }}</div>
    </div>

    <!-- Les pages sondage, avec une transion fade -->
    <hr>
    <transition-group name="fade" tag="div">
      <page v-for="page, pageIndex in poll.pages" :key="page"
            :poll="poll" :page="page" :pageIndex="pageIndex"></page>
    </transition-group>
    <hr>

    <div class="text-center">
      <button :disabled="submitting" type="submit" class="btn btn-primary btn-lg">
        {{ isEditingPoll ? $t('poll.update') : $t('poll.create') }}
      </button>
    </div>
  </form>
</template>

<script>
  import axios from "axios";
  import Bus from '../bus/admin-add-poll';
  import * as Event from '../bus/events';
  import {mapGetters} from "vuex";

  export default {
    data () {
      return {
        submitting: false,
        toDelete: {
          pages: [],
          questions: [],
          propositions: []
        }
      }
    },
    computed: {
      ...mapGetters(['isEditingPoll', 'poll', 'variants', 'formAction'])
    },
    methods: {
      onSubmit() {
        this.submitting = true;
        const $$form = $(this.$el);

        /*
         * Reset des erreurs
         */
        this.poll.title.error = null;
        this.poll.description.error = null;
        this.poll.pages.forEach(page => {
          page.title.error = null;
          page.title.description = null;
          page.questions.forEach(question => {
            question.title.error = null;
            question.propositions.forEach(proposition => {
              proposition.error = null;
            })
          })
        });

        function handleError(entity, error) {
          const field = entity[error['property']];

          if (error['constraintName'] === 'NotBlank' && field.value.length === 0) {
            field.error = error.message
          }
        }

        axios
          .post(this.formAction, $$form.serialize())
          .then(response => {
            if (!response.request.responseURL.endsWith(this.formAction)) {
              window.location.replace(response.request.responseURL);
              return {}
            }
            return response;
          })
          .then(response => response.data)
          .then(errors => {
            if (!errors) {
              return;
            }

            errors.forEach(error => {
              error['entityName'] === 'poll' && handleError(this.poll, error);
              this.poll.pages.forEach(page => {
                error['entityName'] === 'page' && handleError(page, error);
                page.questions.forEach(question => {
                  error['entityName'] === 'question' && handleError(question, error);
                  question.propositions.forEach(proposition => {
                    error['entityName'] === 'proposition' && handleError(proposition, error);
                  });
                });
              });
            });

            this.$nextTick(function() {
              const $$firstFieldWithError = $$form.find('.has-danger:first');
              $(document.body).animate({
                scrollTop: $$firstFieldWithError.offset().top - 100
              });
            });

            this.submitting = false;
          })
          .catch(error => {
            console.error('Erreur durant la validation', error);
            alert('Erreur durant la validation du sondage.');
            this.submitting = false;
          });
      },
      addPageBefore(index = 0) {
        this._addPage(index);
      },
      addPageAfter(index = 0) {
        this._addPage(index + 1);
      },
      removePage (page) {
        const index = this.poll.pages.indexOf(page);
        this.poll.pages.splice(index, 1);
        this.isEditingPoll && 'id' in page && this.toDelete.pages.push(page.id);
      },
      addQuestionBefore(page, questionIndex = 0) {
        this._addQuestion(page, questionIndex);
      },
      addQuestionAfter(page, questionIndex = 0) {
        this._addQuestion(page, questionIndex + 1);
      },
      removeQuestion(page, question) {
        const index = page.questions.indexOf(question);
        page.questions.splice(index, 1);
        this.isEditingPoll && 'id' in question && this.toDelete.questions.push(question.id);
      },
      removeProposition(question, proposition) {
        const index = question.propositions.indexOf(proposition);
        question.propositions.splice(index, 1);
        this.isEditingPoll && 'id' in proposition && this.toDelete.propositions.push(proposition.id);
      },
      _addPage(pageIndex = 0) {
        // Ajout de la page
        this.poll.pages.splice(pageIndex, 0, {
          title: {
            value: '',
            error: null
          },
          description: {
            value: '',
            error: null
          },
          questions: []
        });

        // Puis on y rajoute une question
        this.addQuestionBefore(this.poll.pages[pageIndex]);
      },
      _addQuestion(page, questionIndex = 0) {
        page.questions.splice(questionIndex, 0, {
          title: {
            value: '',
            error: null,
          },
          variant: {
            name: this.variants[Object.keys(this.variants)[0]] // premier élément d'un objet
          },
          propositions: [{
            title: {
              value: '',
              error: null
            }
          }]
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

      Bus.$on(Event.REMOVE_PROPOSITION, this.removeProposition);

      // Ajout dans le store...
      'VARIANTS' in window && this.$store.commit('setVariants', window['VARIANTS']);
      'FORM_ACTION' in window && this.$store.commit('setFormAction', window['FORM_ACTION']);
      'IS_EDITING_POLL' in window && this.$store.commit('pollIsEditing');

      // Doit être fait après les 3 ajouts dans le store ci-dessus
      if ('POLL' in window) {
        const poll = window['POLL'];
        poll.title = {value: poll.title, error: null};
        poll.description = {value: poll.description, error: null};
        poll.pages.forEach(page => {
          page.title = {value: page.title, error: null};
          page.description = {value: page.description, error: null};
          page.questions.forEach(question => {
            question.title = {value: question.title, error: null};
            question.propositions.forEach(proposition => {
              proposition.title = {value: proposition.title, error: null};
            })
          })
        });

        this.$store.commit('setPoll', poll);
      } else {
        this.$store.commit('setPoll', { // L'objet qui va contenir les pages, les questions, et les propositions
          title: {
            value: '', // this.$t = fonction rajoutée par VueI18n,
            error: null
          },
          description: {
            value: '',
            error: null,
          },
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
