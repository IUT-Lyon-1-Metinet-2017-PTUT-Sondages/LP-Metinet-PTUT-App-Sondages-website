<template>
    <transition-group name="fade">
        <div v-for="proposition, propositionIndex in question.propositions" :key="propositionIndex"
             class="row my-q no-gutters">

            <input v-if="isEditingPoll && 'id' in proposition" :value="proposition.id" type="hidden"
                   :name="'poll[pages][' + pageIndex + '][questions][' + questionIndex + '][propositions][' +  propositionIndex + '][id]'">

            <!-- Input radio -->
            <div class="col col-auto kdt-radio col-form-label mr-h">
                <input type="radio" disabled="disabled">
                <label></label>
            </div>

            <!-- Titre de la proposition -->
            <div class="col">
                <input v-model="proposition.title"
                       :name="'poll[pages][' + pageIndex + '][questions][' + questionIndex + '][propositions][' +  propositionIndex + '][title]'"
                       :placeholder="$t('proposition.placeholder.proposition_x', {x: propositionIndex + 1})"
                       class="form-control d-inline-block"
                       required="required">
            </div>

            <!-- Bouton supprimer -->
            <div class="col col-auto ml-h">
                <button class="btn btn-outline-danger"
                        @click.prevent="question.propositions.splice(propositionIndex, 1)"
                        :disabled="question.propositions.length <= 1">&times;</button>
            </div>
        </div>
    </transition-group>
</template>

<script>
  import {mapGetters} from 'vuex';

  export default {
    props: {
      page: {type: Object, required: true},
      pageIndex: {type: Number, required: true},
      question: {type: Object, required: true},
      questionIndex: {type: Number, required: true},
    },
    computed: {
      ...mapGetters(['isEditingPoll'])
    },
    data () {
      return {}
    }
  }
</script>