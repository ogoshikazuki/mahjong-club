import Home from "./components/Home.vue";
import EditMoney from "./components/EditMoney.vue";
import History from "./components/History.vue";
import Aggregate from "./components/Aggregate.vue";
import TenhouLog from "./components/TenhouLog.vue";
import InterpersonalResult from "./components/InterpersonalResult.vue";

export default [
    { name: "home", path: "/", component: Home },
    { name: "history", path: "/history", component: History },
    { name: "edit-money", path: "/edit-money", component: EditMoney },
    { name: "aggregate", path: "/aggregate", component: Aggregate },
    { name: "tenhou-log", path: "/tenhou-log", component: TenhouLog },
    { name: "interpersonal-result", path: "/interpersonal-result", component: InterpersonalResult },
];
