<template>
  <div>
    <v-alert v-if="registeredCount" type="success">{{ registeredCount }}件を登録しました。</v-alert>
    <tenhou-log-downloader @complete="tenhouLogs = $event"></tenhou-log-downloader>
    <tenhou-log-registerer v-if="tenhouLogs.length > 0" :tenhou-logs="tenhouLogs" @registered="registered($event)"></tenhou-log-registerer>
  </div>
</template>

<script>
import TenhouLogDownloader from "./TenhouLogDownloader";
import TenhouLogRegisterer from "./TenhouLogRegisterer";

export default {
  components: {
    TenhouLogDownloader,
    TenhouLogRegisterer,
  },

  data () {
    return {
      tenhouLogs: [],
      registeredCount: null,
    };
  },

  methods: {
    async registered(registeredCount) {
      this.registeredCount = registeredCount;
      this.tenhouLogs = [];
    },
  },
}
</script>
