<template>
    <div>
        <div v-for="proposition, index in question.propositions" class="row my-q no-gutters">
           <div class="col col-auto kdt-radio col-form-label mr-h">
                <input type="radio" disabled="disabled">
                <label></label>
            </div>
            <div class="col">
                <input v-model="proposition.title" class="form-control d-inline-block">
            </div>
            <div class="col col-auto ml-h">
                <button class="btn btn-danger"
                        @click="question.propositions.splice(index, 1)"
                        :disabled="question.propositions.length <= 1">&times;</button>
            </div>
        </div>
    </div>
</template>

<script>
  export default {
    props: {
      question: {type: Object, required: true},
    },
    data () {
      return {}
    }
  }
</script>