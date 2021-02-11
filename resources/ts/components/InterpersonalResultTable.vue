<template>
  <v-simple-table v-loading="loading">
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
</template>

<script lang="ts">
import Vue, { PropType } from 'vue'
import Player from '../types/Player'
import GameResult from '../types/GameResult'
import PlayerCount from '../types/PlayerCount'
export default Vue.extend({
  props: {
    gameResults: {
      type: Array as PropType<GameResult[]>,
      required: true,
    },
    first: {
      type: Number,
      required: true,
    },
    second: {
      type: Number,
      required: true,
    },
    playerCount: {
      type: Number as PropType<PlayerCount>,
      required: true,
    },
  },
  computed: {
    players(): Player[] {
      return this.$store.state.players
    },
    loading(): boolean {
      return this.gameResults.length === 0
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
    firstPlayerName(): string {
      const player: Player | undefined = this.players.find((player: Player) => player.id === this.first)

      if (typeof player === 'undefined') {
        return ''
      }

      return player.name
    },
    secondPlayerName(): string {
      const player: Player | undefined = this.players.find((player: Player) => player.id === this.second)

      if (typeof player === 'undefined') {
        return ''
      }

      return player.name
    },
    firstAverage(): number {
      return this.average(this.first)
    },
    secondAverage(): number {
      return this.average(this.second)
    },
    firstPoint(): number {
      return this.point(this.first)
    },
    secondPoint(): number {
      return this.point(this.second)
    },
    firstTip(): number {
      return this.tip(this.first)
    },
    secondTip(): number {
      return this.tip(this.second)
    },
    firstMoney(): number {
      const point = this.firstPoint + this.firstTip * 2
      if (this.playerCount === 3) {
        return point * 50
      }
      return point * 100
    },
    secondMoney(): number {
      const point = this.secondPoint + this.secondTip * 2
      if (this.playerCount === 3) {
        return point * 50
      }
      return point * 100
    },
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
