<template>
    <el-table :data="tableData" v-loading="loading" max-height="500">
        <el-table-column prop="rate" label="レート"></el-table-column>
        <el-table-column
            v-for="player in players"
            :key="player.id"
            :prop="String(player.id)"
            :label="player.name"
        ></el-table-column>
        <el-table-column width="146">
            <template slot-scope="scope">
                <el-button size="mini" @click="editGameResult(scope.row)"
                    >編集</el-button
                >
                <el-dialog
                    title="編集"
                    :visible.sync="editFormVisible"
                    v-loading="isEditFormLoading"
                >
                    <el-form :model="editForm">
                        <el-form-item label="レート">
                            <el-select v-model="editForm.rate">
                                <el-option label="50" :value="50"></el-option>
                                <el-option label="100" :value="100"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item
                            v-for="player in players"
                            :key="player.id"
                            :label="player.name"
                        >
                            <el-input
                                v-model="editForm.points[player.id]"
                                type="number"
                            ></el-input>
                        </el-form-item>
                    </el-form>
                    <template v-if="editFormErrorMessages">
                        <el-alert
                            type="error"
                            v-for="(message, index) in editFormErrorMessages"
                            :key="index"
                            :title="message"
                        ></el-alert>
                    </template>
                    <span slot="footer" class="dialog-footer">
                        <el-button
                            size="mini"
                            type="primary"
                            @click="updateGameResult"
                            >更新</el-button
                        >
                    </span>
                </el-dialog>
                <el-button
                    size="mini"
                    type="danger"
                    @click="deleteGameResult(scope.row.id)"
                    >削除</el-button
                >
            </template>
        </el-table-column>
    </el-table>
</template>

<script>
import apiClient from "../ApiClient";

export default {
    data: function() {
        return {
            tableData: [],
            players: [],
            loading: true,
            editForm: { id: null, rate: null, points: {} },
            editFormVisible: false,
            isEditFormLoading: false,
            editFormErrorMessages: []
        };
    },

    methods: {
        async load() {
            this.players = await apiClient.getAllPlayers();
            const currentGame = await apiClient.getCurrentGame();
            for (let gameResult of currentGame.gameResults) {
                let row = { rate: gameResult.rate };
                for (let player of this.players) {
                    for (let gameResultPlayer of gameResult.gameResultPlayers) {
                    }
                    row[player.id] = player.id;
                }
                this.tableData.push(row);
            }
            this.tableData = currentGame.gameResults.map(gameResult => {
                const row = { id: gameResult.id, rate: gameResult.rate };
                for (let player of this.players) {
                    for (let gameResultPlayer of gameResult.gameResultPlayers) {
                        if (gameResultPlayer.player_id === player.id) {
                            row[player.id] = gameResultPlayer.point;
                        }
                    }
                }
                return row;
            });

            this.loading = false;
        },

        async deleteGameResult(id) {
            if (!confirm("本当に削除しますか？")) {
                return;
            }
            this.loading = true;

            await apiClient.deleteGameResult(id);
            this.load();
        },

        editGameResult(row) {
            this.resetForm();
            for (let key of Object.keys(row)) {
                if (isNaN(key)) {
                    this.$set(this.editForm, key, row[key]);
                } else {
                    this.$set(this.editForm.points, key, row[key]);
                }
            }
            this.editFormVisible = true;
        },

        resetForm() {
            this.$set(this.editForm, "id", null);
            this.$set(this.editForm, "rate", null);
            for (let player of this.players) {
                this.$set(this.editForm.points, player.id, null);
            }
            this.editFormErrorMessages = [];
        },

        async updateGameResult() {
            this.isEditFormLoading = true;

            const response = await apiClient.updateGameResult(this.editForm);
            this.isEditFormLoading = false;
            if (response.status === 422) {
                this.editFormErrorMessages = (
                    await response.json()
                ).errors.points;
                return;
            }

            this.editFormVisible = false;
            this.loading = true;

            this.load();
        }
    },

    created: function() {
        this.load();
    }
};
</script>
