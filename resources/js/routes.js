import Home from "./components/Home";
import EditMoney from "./components/EditMoney";
import History from "./components/History";
import Aggregate from "./components/Aggregate";
import TenhouLog from "./components/TenhouLog";

export default [
    { name: "home", path: "/", component: Home },
    { name: "history", path: "/history", component: History },
    { name: "edit-money", path: "/edit-money", component: EditMoney },
    { name: "aggregate", path: "/aggregate", component: Aggregate },
    { name: "tenhou-log", path: "/tenhou-log", component: TenhouLog },
];
