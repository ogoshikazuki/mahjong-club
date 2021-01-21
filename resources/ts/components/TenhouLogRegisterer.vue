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

<script lang="ts">
import Vue, { PropType } from "vue";
import apiClient from "../ApiClient";

type GameResult = {
  playerName: string,
  point: number,
  tip: number,
};
type TenhouLog = {
  gameResults: GameResult[],
};
type TenhouLogWithGameResultsText = TenhouLog & {
  gameResultsText: string,
};

type Data = {
  headers: { text: string, value: string }[],
  checked: TenhouLogWithGameResultsText[],
  registering: boolean,
};
type Methods = {
  register: () => Promise<void>,
};
type Computed = {
  items: TenhouLogWithGameResultsText[],
  checkedTenhouLogs: GameResult[][],
};
type Props = {
  tenhouLogs: TenhouLog[],
}

export default Vue.extend<Data, Methods, Computed, Props>({
  props: {
    tenhouLogs: {
      type: Array as PropType<TenhouLog[]>,
      required: true,
    },
  },

  data (): Data {
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
    items(): TenhouLogWithGameResultsText[] {
      return this.tenhouLogs.map((tenhouLog: TenhouLog): TenhouLogWithGameResultsText => {
        const tenhouLogWithGameResultsText: TenhouLogWithGameResultsText = tenhouLog as TenhouLogWithGameResultsText;
        tenhouLogWithGameResultsText["gameResultsText"] = tenhouLog.gameResults.reduce((gameResults, gameResult) => {
            if (gameResults) {
              gameResults += " ";
            }
            return gameResults += `${gameResult.playerName}:${gameResult.point},${gameResult.tip}枚`;
          }, "");
        return tenhouLogWithGameResultsText;
      })
    },

    checkedTenhouLogs(): GameResult[][] {
      return this.checked.map((item: TenhouLogWithGameResultsText) => item.gameResults);
    },
  },

  methods: {
    async register() {
      this.registering = true;

      this.$emit("registered", (await (await apiClient.registerTenhouLog({ tenhou_logs: this.checkedTenhouLogs })).json()).data);
    },
  },
});
</script>
