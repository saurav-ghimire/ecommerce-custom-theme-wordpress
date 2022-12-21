import { Table, Row, Col, Skeleton, Empty } from "antd";
import "./style.scss";
import { ITopWholesaleCustomers } from "types/index";

// Redux
import { connect } from "react-redux";
import { useEffect, useState } from "react";

const TopWholesaleCustomers = (props: any) => {
  const { top_wholesale_customers, i18n, fetching } = props;
  const [dataSource, setDataSource] = useState([]);

  const columns = [
    {
      title: "Name",
      dataIndex: "name",
      key: "name",
      width: "50%",
      render: (value: string, row: ITopWholesaleCustomers, index: number) => (
        <a href={row?.link} target="_blank" rel="noreferrer">
          {value}
        </a>
      )
    },
    {
      title: "Total",
      dataIndex: "total",
      key: "total",
      width: "50%",
      render: (total: string) => (
        <span dangerouslySetInnerHTML={{ __html: total }}></span>
      )
    }
  ];

  useEffect(() => {
    let data: any = [];

    if (top_wholesale_customers.length > 0) {
      top_wholesale_customers.forEach(
        (elem: ITopWholesaleCustomers, i: number) => {
          data = [
            ...data,
            {
              key: i,
              name: elem?.name,
              total: elem?.spent,
              link: elem?.link
            }
          ];
        }
      );
    }

    if (data.length > 0) {
      setDataSource(data);
    }
  }, [top_wholesale_customers]);

  return (
    <Row gutter={[24, 40]} className="top-wholesale-customers">
      <Col span={24}>
        <h3>
          {fetching ? (
            <Skeleton.Button
              style={{ width: 150, height: 30 }}
              active={true}
              size="large"
            />
          ) : (
            i18n?.top_wholesale_customers
          )}
        </h3>
        <Table
          showHeader={false}
          pagination={false}
          dataSource={dataSource}
          columns={columns}
          bordered={true}
          loading={fetching}
          locale={{ emptyText: <Empty description={i18n?.no_data} /> }}
        />
      </Col>
    </Row>
  );
};

const mapStateToProps = (store: any, props: any) => ({
  fetching: store?.dashboard?.fetching,
  top_wholesale_customers: store?.dashboard?.top_wholesale_customers,
  i18n: store?.dashboard?.internationalization
});

export default connect(mapStateToProps)(TopWholesaleCustomers);
