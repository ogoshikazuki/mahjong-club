<template>
  <v-dialog :value="value" @input="$emit('input', $event)" v-loading="loading">
    <v-card>
      <v-card-title>精算提案</v-card-title>
      <v-card-text>
        <v-simple-table class="text-no-wrap">
          <tbody>
            <tr v-for="(suggest, i) in suggest1" :key="i">
              <td>{{ suggest.from }}</td>
              <td><v-icon>mdi-arrow-right</v-icon></td>
              <td>{{ suggest.to }}</td>
              <td>{{ suggest.money }}</td>
            </tr>
          </tbody>
        </v-simple-table>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script lang="ts">
import Vue from 'vue'
import ApiClient from '../ApiClient'
import MoneyPlayer from '../types/MoneyPlayer'

type Suggest = {
  from: string
  to: string
  money: number
}

type Data = {
  moneyPlayers: MoneyPlayer[]
  loading: boolean
}
type Methods = Record<string, never>
type Computed = {
  minusMoneyPlayers: MoneyPlayer[]
  plusMoneyPlayers: MoneyPlayer[]
  suggest1: Suggest[]
}
type Props = {
  value: boolean
}

export default Vue.extend<Data, Methods, Computed, Props>({
  props: {
    value: {
      type: Boolean,
      required: true,
    },
  },

  data() {
    return {
      moneyPlayers: [],
      loading: true,
    }
  },

  computed: {
    minusMoneyPlayers() {
      return [...this.moneyPlayers].sort((a, b) => a.money - b.money).filter(({ money }) => money < 0)
    },

    plusMoneyPlayers() {
      return [...this.moneyPlayers].sort((a, b) => b.money - a.money).filter(({ money }) => money > 0)
    },

    suggest1() {
      if (this.loading) {
        return []
      }

      const result = []

      let minusIndex = 0
      let plusIndex = 0
      let minus = -this.minusMoneyPlayers[minusIndex].money
      let plus = this.plusMoneyPlayers[plusIndex].money

      minus: while (minusIndex < this.minusMoneyPlayers.length) {
        plus: while (plusIndex < this.plusMoneyPlayers.length) {
          if (plus < minus) {
            result.push({
              from: this.minusMoneyPlayers[minusIndex].player.name,
              to: this.plusMoneyPlayers[plusIndex].player.name,
              money: plus,
            })
            minus -= plus
            plusIndex++
            plus = this.plusMoneyPlayers[plusIndex]?.money
            continue plus
          }
          result.push({
            from: this.minusMoneyPlayers[minusIndex].player.name,
            to: this.plusMoneyPlayers[plusIndex].player.name,
            money: minus,
          })
          plus -= minus
          minusIndex++
          minus = -this.minusMoneyPlayers[minusIndex]?.money
          continue minus
        }
      }

      return result
    },
  },

  async created() {
    this.moneyPlayers = (await ApiClient.getCurrentMoney()).money_players
    this.loading = false
  },
})
</script>
