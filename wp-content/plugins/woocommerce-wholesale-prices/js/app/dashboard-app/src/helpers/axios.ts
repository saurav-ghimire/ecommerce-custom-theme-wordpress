import axios from "axios";

declare var dashboard_options: any;
let headers = {};

if (
  typeof dashboard_options !== "undefined" &&
  dashboard_options.nonce !== ""
) {
  headers = {
    "X-WP-Nonce": dashboard_options.nonce
  };
}

export default axios.create({
  baseURL: dashboard_options.root,
  timeout: 0,
  headers: {
    ...headers
  }
});
