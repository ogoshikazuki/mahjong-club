<template>
  <div>
    <transition name="fade" mode="out-in">
      <current-money-games v-if="game === null" @show-game="showGame($event)"></current-money-games>
      <game-result-history
        v-else
        ref="gameResultHistory"
        :game-results="game.gameResults"
        @reload="reloadGame"
      ></game-result-history>
    </transition>
    <v-btn v-if="game !== null" color="secondary" @click="game = null">戻る</v-btn>
  </div>
</template>

<script>
import apiClient from "../ApiClient";
import CurrentMoneyGames from "./CurrentMoneyGames";
import GameResultHistory from "./GameResultHistory";

export default {
  components: {
    CurrentMoneyGames,
    GameResultHistory
  },

  data() {
    return {
      game: null
    };
  },

  methods: {
    async showGame(game) {
      this.game = game;
    },

    async reloadGame() {
      this.$refs.gameResultHistory.loading = true;

      this.game = await apiClient.findGame(this.game.id);

      this.$refs.gameResultHistory.loading = false;
    }
  }
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
  opacity: 0;
}
</style>
