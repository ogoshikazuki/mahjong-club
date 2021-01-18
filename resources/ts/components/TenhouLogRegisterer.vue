<template>
  <div>
    <v-data-table
      :headers="headers"
      :items="items"
      show-select
      v-model="checked"
      item-key="startTime"
      :mobile-breakpoint="null"
      hide-default-footer
      disable-pagination
    ></v-data-table>
    <v-divider></v-divider>
    <v-btn color="primary" @click="register" :loading="registering">チェックしたログを登録</v-btn>
  </div>
</template>

<script>
import apiClient from "../ApiClient";

export default {
  props: {
    tenhouLogs: {
      type: Array,
      required: true,
    },
  },

  data () {
    return {
      headers: [
        { text: "開始時刻", value: "startTime" },
        { text: "結果", value: "gameResultsText" },
      ],
      checked: [],
      registering: false,
    };
  },

  computed: {
    items() {
      return this.tenhouLogs.map(tenhouLog => {
        tenhouLog["gameResultsText"] = tenhouLog.gameResults.reduce((gameResults, gameResult) => {
            if (gameResults) {
              gameResults += " ";
            }
            return gameResults += `${gameResult.playerName}:${gameResult.point},${gameResult.tip}枚`;
          }, "");
        return tenhouLog;
      })
    },

    checkedTenhouLogs() {
      return this.checked.map(item => item.gameResults);
    },
  },

  methods: {
    async register() {
      this.registering = true;

      this.$emit("registered", (await (await apiClient.registerTenhouLog({ tenhou_logs: this.checkedTenhouLogs })).json()).data);
    },
  },
}
</script>
