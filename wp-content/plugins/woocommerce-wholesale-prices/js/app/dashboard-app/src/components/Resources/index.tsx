import { Card, Skeleton } from "antd";
import {
  ThunderboltOutlined,
  ReadOutlined,
  SettingOutlined,
  QuestionCircleOutlined
} from "@ant-design/icons";
import "./style.scss";

// Redux
import { connect } from "react-redux";
import LicenseStatus from "components/LicenseStatus";

const Resources = (props: any) => {
  const { i18n, help_resources_links, fetching } = props;

  if (fetching) {
    return (
      <Card className="side-panel">
        <div className="resources">
          <h3>
            <Skeleton.Button
              style={{ width: 150, height: 30 }}
              active={true}
              size="large"
            />
          </h3>
          <ul>
            <li>
              <Skeleton.Button
                style={{ width: 200, height: 30 }}
                active={true}
                size="large"
              />
            </li>
            <li>
              <Skeleton.Button
                style={{ width: 200, height: 30 }}
                active={true}
                size="large"
              />
            </li>
            <li>
              <Skeleton.Button
                style={{ width: 200, height: 30 }}
                active={true}
                size="large"
              />
            </li>
            <li>
              <Skeleton.Button
                style={{ width: 200, height: 30 }}
                active={true}
                size="large"
              />
            </li>
          </ul>
        </div>
        <LicenseStatus />
      </Card>
    );
  } else
    return (
      <Card className="side-panel">
        <div className="resources">
          <h3>{i18n?.helpful_resources}</h3>
          <ul>
            <li>
              <ThunderboltOutlined />
              &nbsp;
              <a
                href={help_resources_links?.getting_started_guide_link}
                target="_blank"
                rel="noreferrer"
              >
                {i18n?.getting_started_guides}
              </a>
            </li>
            <li>
              <ReadOutlined />
              &nbsp;
              <a
                href={help_resources_links?.read_documentation_link}
                target="_blank"
                rel="noreferrer"
              >
                {i18n?.read_documentation}
              </a>
            </li>
            <li>
              <SettingOutlined />
              &nbsp;
              <a href={help_resources_links?.settings_link}>{i18n?.settings}</a>
            </li>
            <li>
              <QuestionCircleOutlined /> &nbsp;
              <a
                href={help_resources_links?.contact_support}
                target="_blank"
                rel="noreferrer"
              >
                {i18n?.contact_support}
              </a>
            </li>
          </ul>
        </div>
        <LicenseStatus />
      </Card>
    );
};

const mapStateToProps = (store: any, props: any) => ({
  fetching: store?.dashboard?.fetching,
  i18n: store?.dashboard?.internationalization,
  help_resources_links: store?.dashboard?.help_resources_links
});

export default connect(mapStateToProps)(Resources);
