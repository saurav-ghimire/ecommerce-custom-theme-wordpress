export enum EDashboardActionTypes {
  FETCH_DASHBOARD_TEXTS = "FETCH_DASHBOARD_TEXTS",
  FETCH_DASHBOARD_DATA = "FETCH_DASHBOARD_DATA",
  SET_DASHBOARD_DATA = "SET_DASHBOARD_DATA",
  FILTER_QUICK_STATS = "FILTER_QUICK_STATS",
  ACTIVATE_PLUGIN = "ACTIVATE_PLUGIN",
  RECHECK_PLUGIN_STATUS = "RECHECK_PLUGIN_STATUS"
}

export interface IDashboardData {
  fetching: boolean;
  wws_logo: string;
  quick_stats: object;
  top_wholesale_customers: object[];
  recent_wholesale_orders: object[];
  wholesale_orders_link: string;
  filter_options: object;
  internationalization: object;
  license_page_link: string;
  help_resources_links: object;
  license_statuses: object;
  wws_plugins: object;
}

export interface IDashboardOptions {
  root: string;
  nonce: string;
}

export interface ITopWholesaleCustomers {
  id: number;
  name: string;
  spent: number;
  spent_raw: string;
  link: string;
}
