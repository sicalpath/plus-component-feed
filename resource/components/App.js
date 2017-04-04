import React, { Component, PropTypes } from 'react';
import { Tabs, Tab } from 'material-ui/Tabs';

class AppComponent extends Component {

  static contextTypes = {
    router: PropTypes.object.isRequired,
  };

  handleChange = (value) => {
    const { router: { history: { replace } } } = this.context;
    replace(value);
  }

  getPathname() {
    const { router: { route: { location: { pathname } } } } = this.context;
    return pathname;
  }

  render() {
    console.log(this.getPathname());
    return (
      <Tabs
        value={this.getPathname()}
        onChange={this.handleChange}
      >
        <Tab label="分享管理" value="/">a</Tab>
        <Tab label="评论管理" value="/comment">b</Tab>
      </Tabs>
    );
  }

}

export default AppComponent;
