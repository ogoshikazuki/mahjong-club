<template>
  <div>
    <h2>ゲーム詳細</h2>
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>レート</th>
            <th v-for="player in players" :key="player.id">{{ player.name }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="gameResult in gameResults" :key="gameResult.id">
            <td>{{ gameResult.rate }}</td>
            <td v-for="player in players" :key="player.id">{{ showResult(gameResult, player) }}</td>
          </tr>
        </tbody>
      </table>
    </div>
    <button class="btn btn-secondary" @click="$emit('back')">戻る</button>
  </div>
</template>

<script>
import { mapState } from "vuex";

export default {
  props: {
    gameResults: {
      type: Array,
      required: true
    }
  },

  methods: {
    showResult(gameResult, player) {
      for (let gameResultPlayer of gameResult.gameResultPlayers) {
        if (gameResultPlayer.player_id === player.id) {
          return `${gameResultPlayer.point}(${gameResultPlayer.tip}枚)`;
        }
      }
    },
  },

  computed: {
    ...mapState(["players"])
  }
}
</script>