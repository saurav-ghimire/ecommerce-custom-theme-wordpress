import { combineReducers } from "redux";

// Reducers
import dashboardReducer from "./dashboardReducer";

const reducers = combineReducers({
  dashboard: dashboardReducer,
});

export default reducers;
