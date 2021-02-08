<template>
  <div>
    <h2>入力</h2>
    <game-result-input @store-game-result="loadHistory"></game-result-input>
    <hr />
    <h2>履歴</h2>
    <game-result-history
      ref="gameResultHistory"
      :game-results="gameResults"
      @reload="loadHistory"
    ></game-result-history>
  </div>
</template>

<script lang="ts">
import Vue from 'vue'
import GameResultInput from './GameResultInput.vue'
import gameResultHistory from './GameResultHistory.vue'
import Repository from '../Repository'
import GameResult from '../types/GameResult'
import Player from '../types/Player'

interface GameResultHistoryInterface extends Vue {
  loading: boolean
}

export default Vue.extend({
  components: {
    GameResultInput,
    gameResultHistory,
  },

  data(): {
    gameResults: GameResult[]
  } {
    return {
      gameResults: [],
    }
  },

  computed: {
    gameResultHistory(): GameResultHistoryInterface {
      return this.$refs.gameResultHistory as GameResultHistoryInterface
    },
    players(): Player[] {
      return this.$store.state.players
    },
  },

  methods: {
    async loadHistory(): Promise<void> {
      this.gameResultHistory.loading = true

      this.gameResults = []

      this.gameResults = ((await Repository.getCurrentGame().data()) as { gameResults: GameResult[] }).gameResults
      this.gameResultHistory.loading = false
    },
  },

  watch: {
    players(): void {
      this.loadHistory()
    },
  },

  mounted(): void {
    this.gameResultHistory.loading = true
  },
})
</script>
