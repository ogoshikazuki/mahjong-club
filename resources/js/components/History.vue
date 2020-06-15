<template>
  <transition name="fade" mode="out-in">
    <current-money-games v-if="game === null" @show-game="game = $event"></current-money-games>
    <game v-if="game !== null" :game-results="game.gameResults" @back="game = null"></game>
  </transition>
</template>

<script>
import CurrentMoneyGames from "./CurrentMoneyGames";
import Game from "./Game";

export default {
  components: {
    CurrentMoneyGames,
    Game
  },

  data() {
    return {
      game: null
    }
  }
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity .5s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
  opacity: 0;
}
</style>