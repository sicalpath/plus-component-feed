/**
 * The file is admin feeds manage page.
 */

import React, { Component } from 'react';
import PropTypes from 'prop-types'

import { withStyles, createStyleSheet } from 'material-ui/styles';
import Grid from 'material-ui/Grid';
import Card, { CardHeader, CardContent, CardMedia, CardActions } from 'material-ui/Card';
import Dialog, { DialogContent, DialogActions } from 'material-ui/Dialog';
import Snackbar from 'material-ui/Snackbar';
import Avatar from 'material-ui/Avatar';
import Button from 'material-ui/Button';
import IconButton from 'material-ui/IconButton';
import CircularProgress from 'material-ui/Progress/CircularProgress';

import FavoriteIcon from 'material-ui-icons/Favorite';
import Forum from 'material-ui-icons/Forum';
import Delete from 'material-ui-icons/Delete';
import CloseIcon from 'material-ui-icons/Close';

import request, { createRequestURI } from '../utils/request';

const FeedStyleSheet = createStyleSheet('FeedStyleSheet', theme => {
  console.log(theme);
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
    snackbar: {
      open: false,
      message: '',
      vertical: 'bottom',
      horizontal: 'right',
    }
  };

  render() {
    const { classes } = this.props;
    const { feeds = [], del, snackbar } = this.state;

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

        <Dialog open={!! del.feed}>
          <DialogContent>确定要删除吗？</DialogContent>
          <DialogActions>
            { del.ing
              ? <Button disabled>取消</Button>
              : <Button onTouchTap={() => this.handlePushClose()}>取消</Button>
            }
            { del.ing
              ? <Button disabled><CircularProgress size={14} /></Button>
              : <Button color="primary" onTouchTap={() => this.handleDelete()}>删除</Button>
            }
          </DialogActions>
        </Dialog>

        <Snackbar
          anchorOrigin={{ vertical: snackbar.vertical, horizontal: snackbar.horizontal }}
          open={!! snackbar.open}
          message={snackbar.message}
          autoHideDuration={3e3}
          onRequestClose={() => this.handleSnackbarClose()}
          action={[
            <IconButton
              key="snackbar.close"
              color="inherit"
              onTouchTap={() => this.handleSnackbarClose()}
            >
              <CloseIcon />
            </IconButton>
          ]}
        />

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

  handleDelete() {
    const { del: { feed } } = this.state;
    this.setState({
      ...this.state,
      del: { feed, ing: true }
    });
    request.delete(
      createRequestURI(`feeds/${feed}`),
      { validateStatus: status => status === 204 }
    ).then(() => {
      this.handlePushClose();
      this.handlePullFeed(feed);
      this.handleSnackbar({
        message: '删除成功!',
        open: true,
      });
    }).catch(({ response: { data: { message: [ message = '删除失败，请检查网络！' ] = [] } = {} } = {} } = {}) => {
      this.handlePushClose();
      this.handleSnackbar({
        message,
        open: true,
      });
    });
  }

  handlePullFeed(feed) {
    const state = this.state;
    let feeds = [];
    
    state.feeds.forEach(item => {
      if (parseInt(item.id) !== parseInt(feed)) {
        feeds.push(item);
      }
    });

    this.setState({ ...state, feeds });
  }

  handleSnackbar(snackbar = {}) {
    this.setState({
      ...this.state,
      snackbar: { ...this.state.snackbar, ...snackbar }
    });
  }

  handleSnackbarClose() {
    this.handleSnackbar({ open: false, });
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
