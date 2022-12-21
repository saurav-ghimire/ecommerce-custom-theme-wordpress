import { EDashboardActionTypes } from "types/index";

export const dashboardActions = {
  fetchDashboardTexts: (payload: any) => ({
    type: EDashboardActionTypes.FETCH_DASHBOARD_TEXTS,
    payload
  }),
  fetchDashboardData: (payload: any) => ({
    type: EDashboardActionTypes.FETCH_DASHBOARD_DATA,
    payload
  }),
  setDashboardData: (payload: any) => ({
    type: EDashboardActionTypes.SET_DASHBOARD_DATA,
    payload
  }),
  filterQuickStats: (payload: any) => ({
    type: EDashboardActionTypes.FILTER_QUICK_STATS,
    payload
  }),
  activatePlugin: (payload: any) => ({
    type: EDashboardActionTypes.ACTIVATE_PLUGIN,
    payload
  }),
  recheckPluginStatus: (payload: any) => ({
    type: EDashboardActionTypes.RECHECK_PLUGIN_STATUS,
    payload
  })
};
