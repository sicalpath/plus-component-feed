import React, { Component } from 'react';
import { Route } from 'react-router-dom';
import FeedList from './feeds/List';

class FeedsComponent extends Component {
  render() {
    return (
      <div>
        <Route exact component={FeedList}  />
      </div>
    );
  }
}

export default FeedsComponent;
