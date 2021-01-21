<template>
  <div>
    <h2>入力</h2>
    <game-result-input @store-game-result="loadHistory"></game-result-input>
    <hr>
    <h2>履歴</h2>
    <game-result-history ref="gameResultHistory" :game-results="gameResults" @reload="loadHistory"></game-result-history>
  </div>
</template>

<script lang="ts">
import Vue from "vue";
import { mapState } from "vuex";
import GameResultInput from "./GameResultInput.vue";
import gameResultHistory from "./GameResultHistory.vue";
import apiClient from "../ApiClient";

interface GameResultHistoryInterface extends Vue {
  loading: boolean,
}

export default Vue.extend({
  components: {
    GameResultInput,
    gameResultHistory
  },

  data() {
    return {
      gameResults: [],
    }
  },

  computed: {
    ...mapState(["players"])
  },

  methods: {
    async loadHistory() {
      (this.$refs.gameResultHistory as GameResultHistoryInterface).loading = true;

      this.gameResults = [];

      this.gameResults = (await apiClient.getCurrentGame()).gameResults;

      (this.$refs.gameResultHistory as GameResultHistoryInterface).loading = false;
    },
  },

  watch: {
    players() {
      this.loadHistory();
    }
  },

  mounted() {
    (this.$refs.gameResultHistory as GameResultHistoryInterface).loading = true;
  }
});
</script>
