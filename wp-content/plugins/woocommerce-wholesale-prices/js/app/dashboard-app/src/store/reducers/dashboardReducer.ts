import { EDashboardActionTypes, IDashboardData } from "types/index";

export const defaults = {
  fetching: true,
  wws_logo: "",
  quick_stats: {
    wholesale_orders: 0,
    wholesale_revenue: 0
  },
  top_wholesale_customers: [],
  recent_wholesale_orders: [],
  wholesale_orders_link: "",
  filter_options: {},
  internationalization: {},
  license_page_link: "",
  help_resources_links: {},
  license_statuses: {},
  wws_plugins: {}
};

export default function dashboardReducer(
  state: IDashboardData = defaults,
  action: any
) {
  switch (action.type) {
    case EDashboardActionTypes.SET_DASHBOARD_DATA:
      return {
        ...state,
        ...action.payload
      };

    default:
      return state;
  }
}
