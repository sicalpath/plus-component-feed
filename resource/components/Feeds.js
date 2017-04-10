import React, { Component } from 'react';
import { Table, TableBody, TableHeader, TableHeaderColumn, TableRow, TableRowColumn } from 'material-ui/Table';
import FlatButton from 'material-ui/FlatButton';
import request, { createRequestURI, queryString } from '../utils/request';

const tableState = {
  showCheckboxes: false,
  showRowHover: true,
  stripedRows: false,
};

const style = {
  margin: 12,
};

class FeedsComponent extends Component {

  state = {
    feeds: [],
    page: 1,
    pervPage: null,
    nextPage: null,
    limit: 20,
  };

  render() {
    // console.log(this.state.feeds);
    return (
      <div>
        <Table>
          <TableHeader
            adjustForCheckbox={tableState.showCheckboxes}
            displaySelectAll={tableState.showCheckboxes}
          >
            <TableRow>
              <TableHeaderColumn>ID</TableHeaderColumn>
              <TableHeaderColumn>内容</TableHeaderColumn>
              <TableHeaderColumn>作者</TableHeaderColumn>
              <TableHeaderColumn>统计</TableHeaderColumn>
              <TableHeaderColumn>审核</TableHeaderColumn>
              <TableHeaderColumn>操作</TableHeaderColumn>
            </TableRow>
          </TableHeader>
          <TableBody
            displayRowCheckbox={tableState.showCheckboxes}
            showRowHover={tableState.showRowHover}
            stripedRows={tableState.stripedRows}
          >
            {this.state.feeds.map(({ id, feed_title, feed_content, user = {}, ...feed }) => (
              <TableRow>
                <TableRowColumn>{ id }</TableRowColumn>
                <TableRowColumn>
                  {feed_title && (<h3>{ feed_title }</h3>)}
                  { feed_content }
                </TableRowColumn>
                <TableRowColumn>{ user.name }</TableRowColumn>
                <TableRowColumn>
                  <p>点赞：{ feed.feed_digg_count }</p>
                  <p>查看：{ feed.feed_view_count }</p>
                  <p>评论：{ feed.feed_comment_count }</p>
                </TableRowColumn>
                <TableRowColumn>{ this.getAuditStatus(feed.audit_status) }</TableRowColumn>
                <TableRowColumn>
                  {parseInt(feed.audit_status) === 0 && (
                    <div>
                      <a
                        href="#"
                        style={{
                          color: '#03a9f4'
                        }}
                      >
                        通过审核
                      </a>
                    </div>
                  )}
                  <a
                    href="#"
                    style={{
                      color: 'red'
                    }}
                  >
                    删除
                  </a>
                </TableRowColumn>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </div>
    );
  }
  
  componentDidMount() {
    const { limit } = this.state;
    const page = this.getCurrentPage();
    request.get(
      createRequestURI('feeds'),
      {
        validateStatus: status => status === 200,
        params: {
          limit,
          page,
          show_user: 1,
        }
      }
    ).then(({ data: { feeds = [], nextPage = null, pervPage = null } }) => {
      this.setState({
        ...this.state,
        feeds,
        nextPage,
        pervPage,
        page,
      });
    });
  }

  getCurrentPage() {
    const { search } = this.props.location;
    const { page = this.state.page } = queryString(search);

    return page || 1;
  }

  getAuditStatus(status) {
    switch (parseInt(status)) {
      case 1:
        return '已审核';

      case 2:
        return '未通过';

      case 0:
      default:
        return '未审核';
    }
  }
}

export default FeedsComponent;
