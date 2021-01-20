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

<script>
import CurrentMoney from "./CurrentMoney";
import PastMoney from "./PastMoney";
import SuggestResetMoneyDialog from "./SuggestResetMoneyDialog";
import ApiClient from "../ApiClient";

export default {
  components: {
    CurrentMoney,
    PastMoney,
    SuggestResetMoneyDialog,
  },

  data() {
    return {
      resetting: false,
      suggestResetMoneyDialog: false,
    };
  },

  methods: {
    async resetMoney() {
      if (!confirm("本当に精算しますか？")) {
        return;
      }

      this.resetting = true;

      await ApiClient.resetMoney();

      location.reload();
    }
  },
};
</script>

<style scoped>
</style>
