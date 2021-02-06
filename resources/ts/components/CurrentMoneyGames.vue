<template>
  <v-simple-table class="text-no-wrap">
    <thead>
      <tr>
        <th>終了日時</th>
        <th v-for="player in players" :key="player.id">{{ player.name }}</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="game in currentMoneyGames" :key="game.id">
        <td>{{ game.finished_at }}</td>
        <td v-for="player in players" :key="player.id">{{ culculateGamePlayerMoney(game, player) }}</td>
        <td>
          <v-btn color="primary" small @click="$emit('show-game', game)">詳細</v-btn>
        </td>
      </tr>
    </tbody>
  </v-simple-table>
</template>

<script lang="ts">
import Vue from 'vue'
import apiClient from '../ApiClient'
import Player from '../types/Player'
import Game from '../types/Game'

export default Vue.extend({
  data(): {
    currentMoneyGames: Game[]
    loading: boolean
  } {
    return {
      currentMoneyGames: [],
      loading: true,
    }
  },

  computed: {
    players(): Player[] {
      return this.$store.state.players
    },
  },

  methods: {
    culculateGamePlayerMoney(game: Game, player: Player): number {
      let result = 0
      for (let gameResult of game.gameResults) {
        for (let gameResultPlayer of gameResult.gameResultPlayers) {
          if (gameResultPlayer.player_id === player.id) {
            result += (gameResultPlayer.point + gameResultPlayer.tip * 2) * gameResult.rate
          }
        }
      }

      return result
    },
  },

  async created(): Promise<void> {
    this.currentMoneyGames = await apiClient.getCurrentMoneyGames()

    this.loading = false
  },
})
</script>
