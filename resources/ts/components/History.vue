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

<script lang="ts">
import Vue from 'vue'
import Repository from '../Repository'
import CurrentMoneyGames from './CurrentMoneyGames.vue'
import GameResultHistory from './GameResultHistory.vue'
import Game from '../types/Game'

interface GameResultHistoryInterface extends Vue {
  loading: boolean
}

export default Vue.extend({
  components: {
    CurrentMoneyGames,
    GameResultHistory,
  },

  data(): {
    game: Game | null
  } {
    return {
      game: null,
    }
  },

  computed: {
    gameResultHistory(): GameResultHistoryInterface {
      return this.$refs.gameResultHistory as GameResultHistoryInterface
    },
  },

  methods: {
    async showGame(game: Game): Promise<void> {
      this.game = game
    },

    async reloadGame(): Promise<void> {
      this.gameResultHistory.loading = true

      if (this.game === null) {
        return
      }

      this.game = await Repository.findGame(this.game.id)
      this.gameResultHistory.loading = false
    },
  },
})
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
