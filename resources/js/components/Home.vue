<template>
  <div>
    <v-card>
      <v-card-title>未清算</v-card-title>
      <CurrentMoney></CurrentMoney>
      <v-card-actions>
        <v-btn color="warning" href="/money/edit">金額修正</v-btn>
        <v-btn color="secondary" href="history">履歴表示</v-btn>
        <v-btn color="primary" @click="resetMoney">精算</v-btn>
      </v-card-actions>
    </v-card>
    <v-card class="mt-3">
      <v-card-title>精算済</v-card-title>
      <PastMoney></PastMoney>
    </v-card>
  </div>
</template>

<script>
import CurrentMoney from "./CurrentMoney";
import PastMoney from "./PastMoney";
import ApiClient from "../ApiClient";

export default {
  components: {
    CurrentMoney,
    PastMoney,
  },

  data() {
    return {
      resetting: false,
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
