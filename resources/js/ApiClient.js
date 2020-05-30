const urlTemplate = require("url-template");

const URL_TEMPLATE = {
    "player.index": "api/player",
    "game.get-current-game": "api/game/get-current-game",
    "game.result.destroy": "api/game/result/{id}"
};

const _url = (template, parameter = {}) => {
    return urlTemplate.parse(template).expand(parameter);
};

const _get = async (template, urlParameter) => {
    const url = _url(template, urlParameter);
    return (await (await fetch(url)).json()).data;
};

const _delete = (template, id) => {
    const url = _url(template, { id });
    fetch(url, { method: "DELETE" });
};

class ApiClient {
    getAllPlayers() {
        return _get(URL_TEMPLATE["player.index"]);
    }

    getCurrentGame() {
        return _get(URL_TEMPLATE["game.get-current-game"]);
    }

    deleteGameResult(id) {
        _delete(URL_TEMPLATE["game.result.destroy"], id);
    }
}

export default new ApiClient();
