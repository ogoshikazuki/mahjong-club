<template>
  <v-card>
    <v-card-title>対人戦績</v-card-title>
    <v-form>
      <v-container>
        <v-row>
          <v-col cols="4">
            <v-select label="1人目" :items="firstPlayers" item-text="name" item-value="id" v-model="first" />
          </v-col>
          <v-col cols="4">
            <v-select label="2人目" :items="secondPlayers" item-text="name" item-value="id" v-model="second" />
          </v-col>
          <v-col cols="4">
            <v-select label="ゲーム" :items="playerCounts" v-model="playerCount" />
          </v-col>
        </v-row>
      </v-container>
    </v-form>
    <InterpersonalResultTable
      v-if="selected"
      :game-results="gameResults"
      :first="first"
      :second="second"
      :player-count="playerCount"
    ></InterpersonalResultTable>
  </v-card>
</template>

<script lang="ts">
import Vue from 'vue'
import Repository from '../Repository'
import Player from '../types/Player'
import GameResult from '../types/GameResult'
import PlayerCount from '../types/PlayerCount'
import InterpersonalResultTable from './InterpersonalResultTable.vue'
export default Vue.extend({
  components: {
    InterpersonalResultTable,
  },
  data(): {
    first: number | null
    second: number | null
    playerCount: PlayerCount | null
    gameResults: GameResult[]
  } {
    return {
      first: null,
      second: null,
      playerCount: null,
      gameResults: [],
    }
  },
  computed: {
    firstPlayers(): Player[] {
      if (this.second === null) {
        return this.players
      }
      return this.players.filter((player: Player) => player.id !== this.second)
    },
    secondPlayers(): Player[] {
      if (this.first === null) {
        return this.players
      }
      return this.players.filter((player: Player) => player.id !== this.first)
    },
    playerCounts(): { value: number; text: string }[] {
      return [
        { value: 4, text: '四麻' },
        { value: 3, text: '三麻' },
      ]
    },
    selected(): boolean {
      return this.first !== null && this.second !== null && this.playerCount !== null
    },
    players(): Player[] {
      return this.$store.state.players
    },
  },
  async created(): Promise<void> {
    const gameResults: GameResult[] = (await Repository.getAllGameResults().data()) as GameResult[]
    for (const gameResult of gameResults) {
      gameResult.gameResultPlayers.sort((a, b) => b.point - a.point)
    }
    this.gameResults = gameResults
  },
})
</script>
