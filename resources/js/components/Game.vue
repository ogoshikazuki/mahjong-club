<template>
  <div>
    <h2>入力</h2>
    <game-result-input @store-game-result="loadHistory"></game-result-input>
    <hr>
    <h2>履歴</h2>
    <game-result-history ref="gameResultHistory" :game-results="gameResults" @reload="loadHistory"></game-result-history>
  </div>
</template>

<script>
import { mapState } from "vuex";
import GameResultInput from "./GameResultInput";
import gameResultHistory from "./GameResultHistory";
import apiClient from "../ApiClient";

export default {
  components: {
    GameResultInput,
    gameResultHistory
  },

  data() {
    return {
      gameResults: [],
    }
  },

  computed: {
    ...mapState(["players"])
  },

  methods: {
    async loadHistory() {
      this.$refs.gameResultHistory.loading = true;

      this.gameResults = [];

      this.gameResults = (await apiClient.getCurrentGame()).gameResults;

      this.$refs.gameResultHistory.loading = false;
    },
  },

  watch: {
    players() {
      this.loadHistory();
    }
  },

  mounted() {
    this.$refs.gameResultHistory.loading = true;
  }
}
</script>