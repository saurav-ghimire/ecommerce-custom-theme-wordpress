import ReactDOM from "react-dom";
import App from "./App";
import { IDashboardOptions } from "types/index";
import { Result } from "antd";

// Store
import store from "./store";
import { Provider } from "react-redux";

declare var dashboard_options: IDashboardOptions;

if (dashboard_options.root !== "%REACT_APP_ROOT%") {
  ReactDOM.render(
    <Provider store={store}>
      <App />
    </Provider>,
    document.getElementById("wholesale-dashboard")
  );
} else {
  ReactDOM.render(
    <Result
      status="warning"
      title="You have missing environment variables. Please see readme.txt for the proper setup."
    />,
    document.getElementById("wholesale-dashboard")
  );
}
