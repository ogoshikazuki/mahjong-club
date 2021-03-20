<template>
  <div>
    <v-tabs>
      <v-tab>四麻</v-tab>
      <v-tab>三麻</v-tab>
      <v-tab-item>
        <v-simple-table v-loading="loading" class="text-no-wrap">
          <thead>
            <tr>
              <th>名前</th>
              <th>ゲーム数</th>
              <th>平均着順</th>
              <th>祝儀</th>
              <th>金額</th>
              <th>1着</th>
              <th>2着</th>
              <th>3着</th>
              <th>4着</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(result, playerId) in aggregate4" :key="playerId">
              <td>{{ players.find((player) => player.id === Number(playerId)).name }}</td>
              <td>{{ result['game_count'] }}</td>
              <td>
                {{
                  ((1 * result[1] + 2 * result[2] + 3 * result[3] + 4 * result[4]) / result['game_count']).toFixed(2)
                }}
              </td>
              <td>{{ result['tip'] }}</td>
              <td>{{ (result['point'] + result['tip'] * 2) * 100 }}</td>
              <td>{{ result[1] }}({{ ((result[1] / result['game_count']) * 100).toFixed(2) }}%)</td>
              <td>{{ result[2] }}({{ ((result[2] / result['game_count']) * 100).toFixed(2) }}%)</td>
              <td>{{ result[3] }}({{ ((result[3] / result['game_count']) * 100).toFixed(2) }}%)</td>
              <td>{{ result[4] }}({{ ((result[4] / result['game_count']) * 100).toFixed(2) }}%)</td>
            </tr>
          </tbody>
        </v-simple-table>
      </v-tab-item>
      <v-tab-item>
        <v-simple-table v-loading="loading" class="text-no-wrap">
          <thead>
            <tr>
              <th>名前</th>
              <th>ゲーム数</th>
              <th>平均着順</th>
              <th>祝儀</th>
              <th>金額</th>
              <th>1着</th>
              <th>2着</th>
              <th>3着</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(result, playerId) in aggregate3" :key="playerId">
              <td>{{ players.find((player) => player.id === Number(playerId)).name }}</td>
              <td>{{ result['game_count'] }}</td>
              <td>
                {{ ((1 * result[1] + 2 * result[2] + 3 * result[3]) / result['game_count']).toFixed(2) }}
              </td>
              <td>{{ result['tip'] }}</td>
              <td>{{ (result['point'] + result['tip'] * 2) * 50 }}</td>
              <td>{{ result[1] }}({{ ((result[1] / result['game_count']) * 100).toFixed(2) }}%)</td>
              <td>{{ result[2] }}({{ ((result[2] / result['game_count']) * 100).toFixed(2) }}%)</td>
              <td>{{ result[3] }}({{ ((result[3] / result['game_count']) * 100).toFixed(2) }}%)</td>
            </tr>
          </tbody>
        </v-simple-table>
      </v-tab-item>
    </v-tabs>
  </div>
</template>

<script lang="ts">
import Vue from 'vue'
import Repository from '../Repository'
import Player from '../types/Player'

type Aggregate = {
  [key: number]: {
    point: number
    tip: number
    [key: number]: number
  }
}

export default Vue.extend({
  data(): {
    loading: boolean
    aggregate3: Aggregate
    aggregate4: Aggregate
  } {
    return {
      loading: true,
      aggregate3: {},
      aggregate4: {},
    }
  },

  methods: {
    async loadGameResults(): Promise<void> {
      this.loading = true

      this.aggregate3 = (await Repository.aggregateGameResult(3).data()) as Aggregate
      this.aggregate4 = (await Repository.aggregateGameResult(4).data()) as Aggregate

      this.loading = false
    },
  },

  computed: {
    players(): Player[] {
      return this.$store.state.players
    },
  },

  created(): void {
    this.loadGameResults()
  },
})
</script>

<style scoped>
a:not([href]) {
  color: #3490dc;
}
</style>
