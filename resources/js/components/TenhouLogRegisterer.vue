<template>
  <div>
    <v-data-table
      :headers="headers"
      :items="items"
      show-select
      v-model="selected"
      item-key="startTime"
      :mobile-breakpoint="null"
      hide-default-footer
      disable-pagination
    ></v-data-table>
    <v-divider></v-divider>
    <v-btn color="primary" disabled>チェックしたログを登録(近日実装)</v-btn>
  </div>
</template>

<script>
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
      selected: [],
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
  },

  methods: {
  },
}
</script>
