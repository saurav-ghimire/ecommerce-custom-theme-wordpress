import { Select, Row, Col, Popover, Skeleton } from "antd";
import { useState } from "react";
import "./style.scss";

// Redux
import { connect } from "react-redux";
import StatsData from "./StatsData";
import { QuestionCircleOutlined } from "@ant-design/icons";

const { Option } = Select;

const QuickStats = (props: any) => {
  const { i18n, filter_options, fetching } = props;

  const [daysFilter, setDaysFilter] = useState<string>(
    filter_options?.default ?? "30"
  );
  const [triggerFilter, setTriggerFilter] = useState<boolean>(false);

  return (
    <Row gutter={20} className="quick-stats">
      <Col span={12} className="align-items-left">
        <h3>
          {fetching ? (
            <Skeleton.Button
              style={{ width: 150, height: 30 }}
              active={true}
              size="large"
            />
          ) : (
            <>
              {`${i18n?.quick_stats} `}
              <Popover
                placement="right"
                content={
                  <div
                    className="quick-stats-note"
                    dangerouslySetInnerHTML={{ __html: i18n?.quick_stats_note }}
                  ></div>
                }
                trigger="click"
              >
                <QuestionCircleOutlined />
              </Popover>
            </>
          )}
        </h3>
      </Col>
      <Col span={12} className="align-items-right">
        {Object.keys(filter_options).length > 0 ? (
          <Select
            style={{ width: "150px", textAlign: "left" }}
            defaultValue={daysFilter}
            onChange={(value: any) => {
              setDaysFilter(value);
              setTriggerFilter(true);
            }}
          >
            {Object.keys(filter_options?.options).map((key: string) => (
              <Option key={key} value={key}>
                {filter_options?.options?.[key]}
              </Option>
            ))}
          </Select>
        ) : (
          <Skeleton.Button
            style={{ width: 150, height: 30 }}
            active={true}
            size="large"
          />
        )}
      </Col>
      <StatsData
        daysFilter={daysFilter}
        triggerFilter={triggerFilter}
        setTriggerFilter={setTriggerFilter}
      />
    </Row>
  );
};

const mapStateToProps = (store: any, props: any) => ({
  fetching: store?.dashboard?.fetching,
  i18n: store?.dashboard?.internationalization ?? [],
  filter_options: store?.dashboard?.filter_options ?? [],
});

export default connect(mapStateToProps)(QuickStats);
