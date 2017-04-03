<template>
    <transition-group name="fade">
        <div v-for="proposition, propositionIndex in question.propositions" :key="propositionIndex" class="row my-q no-gutters">
            <div class="col col-auto kdt-checkbox col-form-label mr-h">
                <input type="checkbox" disabled="disabled">
                <label></label>
            </div>

            <div class="col" :class="{'has-danger': proposition.title.length == 0}">
                <input v-model="proposition.title" required
                       :name="'poll[pages][' + pageIndex + '][questions][' + questionIndex + '][propositions][' +  propositionIndex + '][title]'"
                       :placeholder="'Proposition n°' + (propositionIndex + 1)" class="form-control d-inline-block">
            </div>

            <div class="col col-auto ml-h">
                <button class="btn btn-outline-danger"
                        @click.prevent="question.propositions.splice(propositionIndex, 1)"
                        :disabled="question.propositions.length <= 1">&times;</button>
            </div>
        </div>
    </transition-group>
</template>

<script>
  export default {
    props: {
      page: {type: Object, required: true},
      pageIndex: {type: Number, required: true},
      question: {type: Object, required: true},
      questionIndex: {type: Number, required: true},
    },
    data () {
      return {}
    }
  }
</script>