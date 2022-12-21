import { all } from "redux-saga/effects";

// Sagas
import * as sagas from "./dashboardSaga";

export default function* rootSaga() {
  yield all([...sagas.actionListener]);
}
