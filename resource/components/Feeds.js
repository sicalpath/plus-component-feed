import React, { Component } from 'react';
import { Table, TableBody, TableHeader, TableHeaderColumn, TableRow, TableRowColumn } from 'material-ui/Table';

const tableState = {
  showCheckboxes: false,
  showRowHover: true,
  stripedRows: false,
};

class FeedsComponent extends Component {

  state = {
    feeds: [],
  };

  render() {
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
            <TableRow>
              <TableRowColumn>1</TableRowColumn>
              <TableRowColumn>这是内容</TableRowColumn>
              <TableRowColumn>Seven</TableRowColumn>
              <TableRowColumn>100</TableRowColumn>
              <TableRowColumn>已审核</TableRowColumn>
              <TableRowColumn></TableRowColumn>
            </TableRow>
          </TableBody>
        </Table>
      </div>
    );
  }
}

export default FeedsComponent;
