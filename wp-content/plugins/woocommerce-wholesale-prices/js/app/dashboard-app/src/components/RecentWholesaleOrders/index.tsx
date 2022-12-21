import { Col, Empty, Row, Skeleton, Table } from "antd";
import "./style.scss";

// Redux
import { connect } from "react-redux";
import { useEffect, useState } from "react";

export interface IRecentWholesaleOrders {
  id: number;
  name: string;
  order_total: number;
  status: string;
  view_order: string;
}

const RecentWholesaleOrders = (props: any) => {
  const { recent_wholesale_orders, wholesale_orders_link, i18n, fetching } =
    props;
  const [dataSource, setDataSource] = useState([]);

  const columns = [
    {
      title: "Order ID",
      dataIndex: "order_id",
      key: "order_id"
    },
    {
      title: "Name",
      dataIndex: "name",
      key: "name"
    },
    {
      title: "Total",
      dataIndex: "total",
      key: "total",
      render: (total: string) => (
        <span dangerouslySetInnerHTML={{ __html: total }}></span>
      )
    },
    {
      title: "Status",
      dataIndex: "status",
      key: "status"
    },
    {
      title: "View Order",
      dataIndex: "view_order",
      key: "view_order",
      render: (view_order: string) => (
        <a href={view_order} target="_blank" rel="noreferrer">
          {i18n?.view_order}
        </a>
      )
    }
  ];

  useEffect(() => {
    let data: any = [];

    if (recent_wholesale_orders.length > 0) {
      recent_wholesale_orders.forEach(
        (elem: IRecentWholesaleOrders, i: number) => {
          data = [
            ...data,
            {
              key: i,
              order_id: `#${elem?.id}`,
              name: elem?.name,
              total: elem?.order_total,
              status: elem?.status,
              view_order: elem?.view_order
            }
          ];
        }
      );
    }

    if (data.length > 0) {
      setDataSource(data);
    }
  }, [recent_wholesale_orders]);

  return (
    <Row gutter={[24, 40]} className="recent-wholesale-orders">
      <Col span={24}>
        <h3>
          {fetching ? (
            <Skeleton.Button
              style={{ width: 150, height: 30 }}
              active={true}
              size="large"
            />
          ) : (
            i18n?.recent_wholesale_orders
          )}
        </h3>
        <Table
          bordered={true}
          showHeader={false}
          pagination={false}
          dataSource={dataSource}
          columns={columns}
          loading={fetching}
          locale={{ emptyText: <Empty description={i18n?.no_data} /> }}
        />
        <br />
        {fetching ? (
          <Skeleton.Button
            style={{ width: 150, height: 30 }}
            active={true}
            size="large"
          />
        ) : (
          <a
            href={wholesale_orders_link}
            target="_blank"
            rel="noreferrer"
            dangerouslySetInnerHTML={{
              __html: i18n?.view_all_wholesale_orders
            }}
          ></a>
        )}
      </Col>
    </Row>
  );
};

const mapStateToProps = (store: any, props: any) => ({
  fetching: store?.dashboard?.fetching,
  recent_wholesale_orders: store?.dashboard?.recent_wholesale_orders,
  i18n: store?.dashboard?.internationalization,
  wholesale_orders_link: store?.dashboard?.wholesale_orders_link
});

export default connect(mapStateToProps)(RecentWholesaleOrders);
