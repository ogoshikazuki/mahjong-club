const urlTemplate = require("url-template");

const URL_TEMPLATE = {
    "player.index": "api/player",
    "game.get-current-game": "api/game/get-current-game",
    "game.get-current-money-games": "api/game/get-current-money-games",
    "game.result.destroy": "api/game/result/{id}",
    "game.result.store": "api/game/result",
    "game.result.update": "api/game/result/{id}",
    "game.result.index": "api/game/result"
};

const headers = {
    "Content-Type": "application/json; charset=utf-8",
    "X-Requested-With": "XMLHttpRequest"
};

const _url = (template, parameter = {}) => {
    return urlTemplate.parse(template).expand(parameter);
};

const _get = async (template, urlParameter) => {
    const url = _url(template, urlParameter);
    return (await (await fetch(url)).json()).data;
};

const _post = async (template, parameters = {}) => {
    const url = _url(template);
    return fetch(url, {
        method: "POST",
        headers,
        body: JSON.stringify(parameters)
    });
};

const _put = (template, id, parameters) => {
    const url = _url(template, { id });
    return fetch(url, {
        method: "PUT",
        headers,
        body: JSON.stringify(parameters)
    });
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

    getCurrentMoneyGames() {
        return _get(URL_TEMPLATE["game.get-current-money-games"]);
    }

    deleteGameResult(id) {
        _delete(URL_TEMPLATE["game.result.destroy"], id);
    }

    updateGameResult(parameters) {
        return _put(
            URL_TEMPLATE["game.result.update"],
            parameters.id,
            parameters
        );
    }

    storeGameResult(parameters) {
        return _post(URL_TEMPLATE["game.result.store"], parameters);
    }

    getAllGameResults() {
        return _get(URL_TEMPLATE["game.result.index"]);
    }
}

export default new ApiClient();
