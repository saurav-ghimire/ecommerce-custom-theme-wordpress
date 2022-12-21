import { takeEvery, put, call } from "redux-saga/effects";
import { EDashboardActionTypes, IResponseGenerator } from "types/index";
import { dashboardActions } from "store/actions/index";
import axiosInstance from "helpers/axios";

export function* fetchDashboardTexts(action: any) {
  try {
    const response: IResponseGenerator = yield call(() =>
      axiosInstance.get(`wholesale/v1/dashboard`, {
        params: { internationalization: true }
      })
    );

    if (response && response.data) {
      yield put(dashboardActions.setDashboardData(response.data));
    } else console.log(response);
  } catch (e) {
    console.log(e);
  }
}

export function* fetchDashboardData(action: any) {
  try {
    const response: IResponseGenerator = yield call(() =>
      axiosInstance.get(`wholesale/v1/dashboard`)
    );

    if (response && response.data) {
      yield put(dashboardActions.setDashboardData(response.data));
    } else console.log(response);

    yield put(dashboardActions.setDashboardData({ fetching: false }));
  } catch (e) {
    console.log(e);
  }
}

export function* filterQuickStats(action: any) {
  try {
    const { daysFilter, successCB, failCB } = action.payload;

    const response: IResponseGenerator = yield call(() =>
      axiosInstance.get(`wholesale/v1/dashboard`, {
        params: { daysFilter }
      })
    );

    if (response && response.data) {
      yield put(dashboardActions.setDashboardData(response.data));

      if (typeof successCB === "function") successCB(response.data);
    } else if (typeof failCB === "function") failCB();
  } catch (e) {
    console.log(e);
  }
}

export function* activatePlugin(action: any) {
  try {
    const { plugin_name, successCB, failCB } = action.payload;

    const response: IResponseGenerator = yield call(() =>
      axiosInstance.get(`wholesale/v1/dashboard`, {
        params: { activate_plugin: plugin_name }
      })
    );

    if (response && response.data) {
      if (typeof successCB === "function") successCB(response.data);
    } else if (typeof failCB === "function") failCB(response.data);
  } catch (e) {
    console.log(e);
  }
}

export function* recheckPluginStatus(action: any) {
  try {
    const { successCB, failCB } = action.payload;

    const response: IResponseGenerator = yield call(() =>
      axiosInstance.get(`wholesale/v1/dashboard`, {
        params: { recheck_plugin_status: true }
      })
    );

    if (response && response.data) {
      yield put(
        dashboardActions.setDashboardData({
          license_statuses: response.data?.license_statuses
        })
      );

      if (typeof successCB === "function") successCB(response.data);
    } else if (typeof failCB === "function") failCB(response.data);
  } catch (e) {
    console.log(e);
  }
}

export const actionListener = [
  takeEvery(EDashboardActionTypes.FETCH_DASHBOARD_TEXTS, fetchDashboardTexts),
  takeEvery(EDashboardActionTypes.FETCH_DASHBOARD_DATA, fetchDashboardData),
  takeEvery(EDashboardActionTypes.FILTER_QUICK_STATS, filterQuickStats),
  takeEvery(EDashboardActionTypes.ACTIVATE_PLUGIN, activatePlugin),
  takeEvery(EDashboardActionTypes.RECHECK_PLUGIN_STATUS, recheckPluginStatus)
];
