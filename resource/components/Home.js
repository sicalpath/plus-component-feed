import React, { Component } from 'react';
import Paper from 'material-ui/Paper';
import Subheader from 'material-ui/Subheader';
import { GridList, GridTile } from 'material-ui/GridList';
import IconButton from 'material-ui/IconButton';
import FeedIcon from 'material-ui/svg-icons/communication/rss-feed';
import CommentIcon from 'material-ui/svg-icons/communication/forum';

class HomeComponent extends Component {
  render() {
    return (
      <div>
        <Paper
          style={{
            padding: 12
          }}
          zDepth={1}
        >
          分享动态管理
        </Paper>

        <Subheader>动态统计</Subheader>
        <GridList>
          <GridTile
            title="动态数"
            subtitle={1290}
            actionIcon={<IconButton><FeedIcon color="white" /></IconButton>}
            actionPosition="right"
            style={{
              background: '#E91E63'
            }}
          />
          <GridTile
            title="评论数"
            subtitle={193823}
            actionIcon={<IconButton><CommentIcon color="white" /></IconButton>}
            actionPosition="right"
            style={{
              background: '#4DB6AC'
            }}
          />
        </GridList>

      </div>
    );
  }
}

export default HomeComponent;
