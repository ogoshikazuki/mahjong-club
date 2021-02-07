<template>
  <div>
    <v-card>
      <v-card-title>未清算</v-card-title>
      <CurrentMoney></CurrentMoney>
      <v-card-actions>
        <v-btn color="warning" :to="{ name: 'edit-money' }">金額修正</v-btn>
        <v-btn color="secondary" :to="{ name: 'history' }">履歴表示</v-btn>
        <v-btn color="purple" dark @click="suggestResetMoneyDialog = true">精算提案</v-btn>
        <v-btn color="primary" @click="resetMoney">精算</v-btn>
      </v-card-actions>
    </v-card>
    <v-card class="mt-3">
      <v-card-title>精算済</v-card-title>
      <PastMoney></PastMoney>
    </v-card>
    <SuggestResetMoneyDialog v-model="suggestResetMoneyDialog"></SuggestResetMoneyDialog>
  </div>
</template>

<script lang="ts">
import Vue from 'vue'
import CurrentMoney from './CurrentMoney.vue'
import PastMoney from './PastMoney.vue'
import SuggestResetMoneyDialog from './SuggestResetMoneyDialog.vue'
import Repository from '../Repository'

export default Vue.extend({
  components: {
    CurrentMoney,
    PastMoney,
    SuggestResetMoneyDialog,
  },

  data(): {
    resetting: boolean
    suggestResetMoneyDialog: boolean
  } {
    return {
      resetting: false,
      suggestResetMoneyDialog: false,
    }
  },

  methods: {
    async resetMoney(): Promise<void> {
      if (!confirm('本当に精算しますか？')) {
        return
      }

      this.resetting = true

      await Repository.resetMoney()

      location.reload()
    },
  },
})
</script>

<style scoped></style>
