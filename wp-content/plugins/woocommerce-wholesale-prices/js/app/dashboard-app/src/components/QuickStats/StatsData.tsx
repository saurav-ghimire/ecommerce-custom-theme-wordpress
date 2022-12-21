import { Card, Col, Skeleton, Space } from "antd";
import { useEffect, useState } from "react";

// Redux
import { dashboardActions } from "store/actions";
import { bindActionCreators, Dispatch } from "redux";
import { connect } from "react-redux";

const { filterQuickStats } = dashboardActions;

const StatsData = (props: any) => {
  const {
    daysFilter,
    quick_stats,
    i18n,
    actions,
    triggerFilter,
    setTriggerFilter,
    loading,
  } = props;

  const [fetching, setFetching] = useState(false);

  useEffect(() => {
    if (triggerFilter) {
      setFetching(true);
      actions.filterQuickStats({
        daysFilter,
        successCB: () => {
          setFetching(false);
        },
        failCB: () => {
          setFetching(false);
        },
      });
      setTriggerFilter(false);
    }
  }, [actions, daysFilter, triggerFilter, setTriggerFilter]);

  return (
    <>
      <Col span={12}>
        <Card>
          {fetching || loading ? (
            <>
              <Space direction="vertical" size="large">
                <Skeleton.Button
                  style={{ width: 150, height: 54 }}
                  active={true}
                  size="large"
                />
                <Skeleton.Button
                  style={{ width: 100, height: 44 }}
                  active={true}
                  size="large"
                />
              </Space>
            </>
          ) : (
            <>
              <h1>{quick_stats?.wholesale_orders}</h1>
              <span>{i18n?.wholesale_orders}</span>
            </>
          )}
        </Card>
      </Col>

      <Col span={12}>
        <Card>
          {fetching || loading ? (
            <>
              <Space direction="vertical" size="large">
                <Skeleton.Button
                  style={{ width: 150, height: 54 }}
                  active={true}
                  size="large"
                />
                <Skeleton.Button
                  style={{ width: 100, height: 44 }}
                  active={true}
                  size="large"
                />
              </Space>
            </>
          ) : (
            <>
              <h1
                dangerouslySetInnerHTML={{
                  __html: quick_stats?.wholesale_revenue,
                }}
              ></h1>
              <span>{i18n?.wholesale_revenue}</span>
            </>
          )}
        </Card>
      </Col>
    </>
  );
};

const mapStateToProps = (store: any, props: any) => ({
  loading: store?.dashboard?.fetching,
  quick_stats: store.dashboard?.quick_stats,
  i18n: store?.dashboard?.internationalization,
});

const mapDispatchToProps = (dispatch: Dispatch) => ({
  actions: bindActionCreators(
    {
      filterQuickStats,
    },
    dispatch
  ),
});

export default connect(mapStateToProps, mapDispatchToProps)(StatsData);
