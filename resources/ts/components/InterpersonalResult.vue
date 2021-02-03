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
    <v-simple-table v-if="selected" v-loading="loading">
      <thead>
        <tr>
          <th>{{ gameCount }}ゲーム</th>
          <th>{{ firstPlayerName }}</th>
          <th>{{ secondPlayerName }}</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th>平均着順</th>
          <td>{{ firstAverage.toFixed(2) }}</td>
          <td>{{ secondAverage.toFixed(2) }}</td>
        </tr>
        <tr>
          <th>祝儀</th>
          <td>{{ firstTip }}</td>
          <td>{{ secondTip }}</td>
        </tr>
        <tr>
          <th>金額</th>
          <td>{{ firstMoney }}</td>
          <td>{{ secondMoney }}</td>
        </tr>
      </tbody>
    </v-simple-table>
  </v-card>
</template>

<script lang="ts">
import Vue from 'vue'
import { mapState } from 'vuex'
import ApiClient from '../ApiClient'
import Player from '../types/Player'
import GameResult from '../types/GameResult'
type PlayerCount = 3 | 4
interface Data {
  first: number | null
  second: number | null
  playerCount: PlayerCount | null
  loading: boolean
  gameResults: GameResult[]
}
export default Vue.extend({
  data(): Data {
    return {
      first: null,
      second: null,
      playerCount: null,
      loading: true,
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
    firstPlayerName(): string {
      return this.players.find((player: Player) => player.id === this.first).name
    },
    secondPlayerName(): string {
      return this.players.find((player: Player) => player.id === this.second).name
    },
    targetGameResults(): GameResult[] {
      return this.gameResults.filter((gameResult) => {
        const gameResultPlayers = gameResult.gameResultPlayers
        return (
          gameResultPlayers.length === this.playerCount &&
          gameResultPlayers.some((gameResultPlayer) => gameResultPlayer.player_id === this.first) &&
          gameResultPlayers.some((gameResultPlayer) => gameResultPlayer.player_id === this.second)
        )
      })
    },
    gameCount(): number {
      return this.targetGameResults.length
    },
    firstAverage(): number | null {
      if (this.first === null) {
        return null
      }
      return this.average(this.first)
    },
    secondAverage(): number | null {
      if (this.second === null) {
        return null
      }
      return this.average(this.second)
    },
    firstPoint(): number | null {
      if (this.first === null) {
        return null
      }
      return this.point(this.first)
    },
    secondPoint(): number | null {
      if (this.second === null) {
        return null
      }
      return this.point(this.second)
    },
    firstTip(): number | null {
      if (this.first === null) {
        return null
      }
      return this.tip(this.first)
    },
    secondTip(): number | null {
      if (this.second === null) {
        return null
      }
      return this.tip(this.second)
    },
    firstMoney(): number | null {
      if (this.firstPoint === null || this.firstTip === null) {
        return null
      }
      const point = this.firstPoint + this.firstTip * 2
      if (this.playerCount === 3) {
        return point * 50
      }
      return point * 100
    },
    secondMoney(): number | null {
      if (this.secondPoint === null || this.secondTip === null) {
        return null
      }
      const point = this.secondPoint + this.secondTip * 2
      if (this.playerCount === 3) {
        return point * 50
      }
      return point * 100
    },
    ...mapState(['players']),
  },
  async created() {
    const gameResults: GameResult[] = await ApiClient.getAllGameResults()
    for (const gameResult of gameResults) {
      gameResult.gameResultPlayers.sort((a, b) => b.point - a.point)
    }
    this.gameResults = gameResults
    this.loading = false
  },
  methods: {
    average(playerId: number): number {
      const finishOrderTotal = this.targetGameResults.reduce((finishOrderTotal, gameResult) => {
        let finishOrder = 1
        for (const gameResultPlayer of gameResult.gameResultPlayers) {
          if (gameResultPlayer.player_id === playerId) {
            finishOrderTotal += finishOrder
            return finishOrderTotal
          }
          finishOrder++
        }
        return finishOrderTotal
      }, 0)
      return finishOrderTotal / this.gameCount
    },
    point(playerId: number): number {
      return this.targetGameResults.reduce((total, gameResult) => {
        const gameResultPlayer = gameResult.gameResultPlayers.find(
          (gameResultPlayer) => gameResultPlayer.player_id === playerId
        )
        if (gameResultPlayer === undefined) {
          return total
        }
        total += gameResultPlayer.point
        return total
      }, 0)
    },
    tip(playerId: number): number {
      return this.targetGameResults.reduce((total, gameResult) => {
        const gameResultPlayer = gameResult.gameResultPlayers.find(
          (gameResultPlayer) => gameResultPlayer.player_id === playerId
        )
        if (gameResultPlayer === undefined) {
          return total
        }
        total += gameResultPlayer.tip
        return total
      }, 0)
    },
  },
})
</script>
