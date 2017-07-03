/**
 * The file is Plus `feed-component` app.
 *
 * @author Seven Du <shiweidu@outlook.com>
 */

import React, { Component } from 'react';
import PropTypes from 'prop-types'
import { matchPath, withRouter } from 'react-router';
import { Route } from 'react-router-dom';
import { withStyles, createStyleSheet } from 'material-ui/styles';
import AppBar from 'material-ui/AppBar';
import Tabs, { Tab } from 'material-ui/Tabs';
import Home from './components/Home';
import Feed from './components/Feed';

const AppStyleSheet = createStyleSheet('AppStyleSheet', theme => ({
  root: {}
}));

class App extends Component
{
  static propTypes = {
    match: PropTypes.object.isRequired,
    location: PropTypes.object.isRequired,
    history: PropTypes.object.isRequired,
    classes: PropTypes.object.isRequired,
  }

  /**
   * Get router pathname.
   *
   * @return {String}
   * @author Seven Du <shiweidu@outlook.com>
   */
  getPathname() {
    const { location: { pathname = '/' } } = this.props;

    return pathname;
  }

  /**
   * Match pathname.
   *
   * @return {Integer} route's index.
   * @author Seven Du <shiweidu@outlook.com>
   */
  matchPath() {
    const pathname = this.getPathname();

    if (matchPath(pathname, { exact: true })) {
      return 0;
    } else if (matchPath(pathname, { path: '/feeds' })) {
      return 1;
    } else if (matchPath(pathname, { path: '/comments' })) {
      return 2;
    }

    return 0;
  }

  /**
   * Route change handle.
   *
   * @param {Object} event
   * @param {Integer} index
   * @return {void}
   * @author Seven Du <shiweidu@outlook.com>
   */
  handleChange = (event, index) => {
    const { history: { replace } } = this.props;

    switch (index) {
      case 2:
        replace('/comments');
        break;

      case 1:
        replace('/feeds');
        break;

      case 0:
      default:
        replace('/');
        break;
    }
  };

  /**
   * Rende the component view.
   *
   * @return {Object}
   * @author Seven Du <shiweidu@outlook.com>
   */
  render() {
    const { classes } = this.props;

    return (
      <div className={classes.root}>
        <AppBar position="static">
          <Tabs
            index={this.matchPath()}
            onChange={this.handleChange}
          >
            <Tab label="基础信息" />
            <Tab label="动态管理" />
            <Tab label="评论管理" />
          </Tabs>
        </AppBar>

        <Route exact path="/" component={Home} />
        <Route path="/feeds" component={Feed} />

      </div>
    );
  }
}

export default withRouter(
  withStyles(AppStyleSheet)(App)
);
