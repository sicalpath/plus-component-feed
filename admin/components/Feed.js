/**
 * The file is admin feeds manage page.
 */

import React, { Component } from 'react';
import PropTypes from 'prop-types'

import { withStyles, createStyleSheet } from 'material-ui/styles';
import Grid from 'material-ui/Grid';
import Card, { CardHeader, CardContent, CardMedia, CardActions } from 'material-ui/Card';
import Dialog, { DialogContent, DialogActions } from 'material-ui/Dialog';
import Avatar from 'material-ui/Avatar';
import Button from 'material-ui/Button';
import IconButton from 'material-ui/IconButton';

import FavoriteIcon from 'material-ui-icons/Favorite';
import Forum from 'material-ui-icons/Forum';
import Delete from 'material-ui-icons/Delete';

import request, { createRequestURI } from '../utils/request';

const FeedStyleSheet = createStyleSheet('FeedStyleSheet', theme => {
  return {
    root: {
      padding: theme.spacing.unit * 2,
    },
    flexGrow: {
      flex: '1 1 auto'
    },
  };
});

class Feed extends Component
{
  static propTypes = {
    classes: PropTypes.object.isRequired,
  };

  state = {
    feeds: [],
    del: {
      feed: null,
      ing: false,
    },
  };

  render() {
    const { classes } = this.props;
    const { feeds = [], del } = this.state;

    return (
      <div>
        <Grid container gutter={24} className={classes.root}>

          { feeds.map(({
            id,
            created_at,
            feed_content: content,
            images: [],
            user: { name, id: user_id } = {},
            feed_digg_count: digg_count = 0,
            feed_comment_count: comment_count = 0,
            expanded = false,
          }) => (

            <Grid item xs={12} sm={6} key={id}>
              <Card>

                <CardHeader
                  avatar={<Avatar>{name[0]}</Avatar>}
                  title={`${name} (${user_id})`}
                  subheader={created_at}
                />

                <CardContent>
                  {content}
                </CardContent>

                <CardMedia>
                
                </CardMedia>

                <CardActions>

                  <Button disabled>
                    <FavoriteIcon />&nbsp;{digg_count}
                  </Button>

                  <Button disabled>
                    <Forum />&nbsp;{comment_count}
                  </Button>

                  <div className={classes.flexGrow} />

                  <IconButton
                    onTouchTap={() => this.handlePushDelete(id)}
                  >
                    <Delete />
                  </IconButton>

                </CardActions>

              </Card>
            </Grid>

          )) }

        </Grid>

        <Dialog open={del.feed}>
          <DialogContent>确定要删除吗？</DialogContent>
          <DialogActions>
            <Button onTouchTap={() => this.handlePushClose()}>取消</Button>
            <Button color="primary">删除</Button>
          </DialogActions>
        </Dialog>

      </div>
    );
  }

  handlePushDelete(feed) {
    const state = this.state;
    this.setState({
      ...state,
      del: { feed, ing: false }
    });
  }

  handlePushClose() {
    this.setState({
      ...this.state,
      del: { feed: null, ing: false }
    });
  }

  componentDidMount() {
    request.get(
      createRequestURI('feeds'),
      { validateStatus: status => status === 200 }
    ).then(({ data }) => {
      this.setState({
        feeds: data,
      });
    });
  }
}

export default withStyles(FeedStyleSheet)(Feed);
