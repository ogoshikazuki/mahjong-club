<template>
  <div>
    <tenhou-log-downloader @complete="tenhouLogs = $event"></tenhou-log-downloader>
    <tenhou-log-registerer v-if="tenhouLogs.length > 0" :tenhou-logs="tenhouLogs"></tenhou-log-registerer>
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
    };
  },
}
</script>
