/* eslint-disable jsx-a11y/anchor-is-valid */
import { message, Skeleton, Tooltip } from "antd";
import {
  CheckSquareOutlined,
  WarningOutlined,
  IssuesCloseOutlined,
  DisconnectOutlined,
  CloseCircleOutlined,
  LoadingOutlined,
  ApiOutlined
} from "@ant-design/icons";
import "./style.scss";

// Redux
import { dashboardActions } from "store/actions";
import { bindActionCreators, Dispatch } from "redux";
import { connect } from "react-redux";
import { useState } from "react";

const { activatePlugin, recheckPluginStatus } = dashboardActions;

const DisplayLicenseStatus = (props: any) => {
  const { id, license_statuses } = props;

  const status = license_statuses?.[id]?.status;
  const tooltip = license_statuses?.[id]?.tooltip;
  const text = license_statuses?.[id]?.text;

  if (status === "inactive") {
    return (
      <li key={id}>
        <IssuesCloseOutlined style={{ color: "orange" }} />
        <span
          dangerouslySetInnerHTML={{
            __html: ` ${text}`
          }}
        ></span>
      </li>
    );
  } else if (status === "expired") {
    return (
      <li key={id}>
        <WarningOutlined style={{ color: "red" }} />
        <span
          dangerouslySetInnerHTML={{
            __html: ` ${text}`
          }}
        ></span>
      </li>
    );
  } else if (status === "invalid") {
    return (
      <li key={id}>
        <CloseCircleOutlined style={{ color: "#ffc107" }} />
        <Tooltip placement="right" title={tooltip}>
          <span
            dangerouslySetInnerHTML={{
              __html: ` ${text}`
            }}
          ></span>
        </Tooltip>
      </li>
    );
  }
  // Licenses is active
  return (
    <li key={id}>
      <CheckSquareOutlined style={{ color: "green" }} />
      <span
        dangerouslySetInnerHTML={{
          __html: ` ${text}`
        }}
      ></span>
    </li>
  );
};

const LicenseStatus = (props: any) => {
  const {
    i18n,
    license_page_link,
    license_statuses,
    wws_plugins,
    fetching,
    actions
  } = props;

  const [activating, setActivating] = useState(false);
  const [pluginName, setPluginName] = useState("");

  const [fetchingPluginStatus, setFetchingPluginStatus] = useState(false);

  // Installed but deactivated plugins
  let installedPlugins = Object.values(wws_plugins).filter((data: any) => {
    return data?.installed ? true : false;
  });

  const activatePlugin = (key: string) => {
    setActivating(true);
    setPluginName(key);
    actions.activatePlugin({
      plugin_name: key,
      successCB: (response: any) => {
        setActivating(false);
        message.success(response?.message);
        setTimeout(function () {
          window.location.reload();
        }, 1000);
      },
      failCB: (response: any) => {
        setActivating(false);
        message.error(response?.message);
        setTimeout(function () {
          window.location.reload();
        }, 1000);
      }
    });
  };

  const checkPluginStatuses = () => {
    setFetchingPluginStatus(true);
    actions.recheckPluginStatus({
      successCB: (response: any) => {
        setFetchingPluginStatus(false);
        message.success(response?.message);
      },
      failCB: (response: any) => {
        setFetchingPluginStatus(false);
        message.error(response?.message);
      }
    });
  };

  if (fetching)
    // Loading / Skeleton
    return (
      <div className="license-status">
        <h3>
          <Skeleton.Button
            style={{ width: 150, height: 30 }}
            active={true}
            size="large"
          />
        </h3>
        <ul>
          {[1, 2, 3].map((key: number) => {
            return (
              <li key={key}>
                <Skeleton.Button
                  style={{ width: 200, height: 30 }}
                  active={true}
                  size="large"
                />
              </li>
            );
          })}
        </ul>
      </div>
    );
  // Licenses are Active
  else
    return (
      <div className="license-status">
        <h3>{i18n?.license_activation_status}</h3>
        <ul>
          {Object.keys(wws_plugins).map((key: string) => {
            // Plugin is installed and active
            // Display license status
            if (wws_plugins?.[key]?.installed && wws_plugins?.[key]?.active)
              return (
                <DisplayLicenseStatus
                  key={key}
                  id={key}
                  license_statuses={license_statuses}
                />
              );
            // Plugin is installed but not active
            else if (
              wws_plugins?.[key]?.installed &&
              wws_plugins?.[key]?.active === false
            )
              return (
                <li key={key}>
                  <DisconnectOutlined />
                  <span
                    dangerouslySetInnerHTML={{
                      __html: ` ${wws_plugins?.[key]?.name} `
                    }}
                  ></span>
                  <Tooltip placement="right" title={i18n?.click_to_activate}>
                    <a href="#" onClick={() => activatePlugin(key)}>
                      ({i18n?.activate_plugin})
                      <LoadingOutlined
                        style={{
                          marginLeft: "10px",
                          display:
                            pluginName === key && activating
                              ? "inline-block"
                              : "none"
                        }}
                        spin
                      />
                    </a>
                  </Tooltip>
                </li>
              );
            // Plugin is not installed and not active
            // Display Learn More link
            else
              return (
                <li key={key}>
                  <ApiOutlined />
                  &nbsp;
                  {wws_plugins?.[key]?.name}&nbsp;(
                  <a
                    href={wws_plugins?.[key]?.learn_more_link}
                    target="_blank"
                    rel="noreferrer"
                    dangerouslySetInnerHTML={{ __html: i18n?.learn_more }}
                  />
                  )
                </li>
              );
          })}
          {/* View License Link if atleast 1 premium is  */}
          {installedPlugins.length > 0 ? (
            <>
              <li>
                <a
                  href={license_page_link}
                  dangerouslySetInnerHTML={{ __html: i18n?.view_licenses }}
                ></a>
              </li>
              <li>
                <Tooltip
                  placement="right"
                  title={i18n?.recheck_plugin_status_tooltip}
                >
                  <a href="#" onClick={() => checkPluginStatuses()}>
                    {i18n?.recheck_plugin_status}
                  </a>
                  <LoadingOutlined
                    style={{
                      marginLeft: "10px",
                      display: fetchingPluginStatus ? "inline-block" : "none"
                    }}
                    spin
                  />
                </Tooltip>
              </li>
            </>
          ) : (
            ""
          )}
        </ul>
      </div>
    );
};

const mapStateToProps = (store: any, props: any) => ({
  fetching: store?.dashboard?.fetching,
  i18n: store?.dashboard?.internationalization,
  license_page_link: store?.dashboard?.license_page_link,
  license_statuses: store?.dashboard?.license_statuses,
  wws_plugins: store?.dashboard?.wws_plugins
});

const mapDispatchToProps = (dispatch: Dispatch) => ({
  actions: bindActionCreators(
    {
      activatePlugin,
      recheckPluginStatus
    },
    dispatch
  )
});

export default connect(mapStateToProps, mapDispatchToProps)(LicenseStatus);
