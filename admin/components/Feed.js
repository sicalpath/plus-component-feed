/**
 * The file is admin feeds manage page.
 */

import React, { Component } from 'react';
import PropTypes from 'prop-types'

import { withStyles, createStyleSheet } from 'material-ui/styles';
import Grid from 'material-ui/Grid';
import Card, { CardHeader, CardContent, CardMedia, CardActions } from 'material-ui/Card';
import Typography from 'material-ui/Typography';
import Dialog, { DialogContent, DialogActions } from 'material-ui/Dialog';
import Snackbar from 'material-ui/Snackbar';
import Avatar from 'material-ui/Avatar';
import Button from 'material-ui/Button';
import IconButton from 'material-ui/IconButton';
import CircularProgress from 'material-ui/Progress/CircularProgress';
import Drawer from 'material-ui/Drawer';
import Chip from 'material-ui/Chip';

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
    drawer: {
      width: 450,
      overflowY: 'auto'
    },
    drawerImage: {
      width: '100%'
    },
    drawerImageTitle: {
      width: '100%',
      position: "absolute",
      bottom: 0,
      background: 'rgba(255, 255, 255, .4)',
      color: '#fff'
    },
    drawerRow: {
      display: 'flex',
      justifyContent: 'flex-start',
      flexWrap: 'wrap',
    },
    chip: {
      margin: theme.spacing.unit,
    }
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
    },
    drawer: null,
  };

  render() {
    const { classes } = this.props;
    const { feeds = [], del, snackbar, drawer } = this.state;

    return (
      <div>
        <Grid container gutter={24} className={classes.root}>

          { feeds.map(({
            id,
            created_at,
            feed_content: content,
            images = [],
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

                <CardContent onTouchTap={() => this.handleRequestDrawer(id)}>
                  <Typography>
                    #{id}
                  </Typography>
                  {content}
                </CardContent>

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

        <Drawer
          open={!! drawer}
          anchor="right"
          onRequestClose={() => this.handleDrawerClose()}
        >
          {this.makeDrawerContent(drawer)}
        </Drawer>

      </div>
    );
  }

  handleRequestDrawer(feed) {
    this.setState({
      ...this.state,
      drawer: feed,
    });
  }

  handleDrawerClose() {
    this.setState({
      ...this.state,
      drawer: null,
    });
  }

  makeDrawerContent(feed_id = null) {
    console.log(feed_id);
    if (! feed_id) {
      return null;
    }

    let feed = null;
    for (let index in this.state.feeds) {
      const item = this.state.feeds[index];
      if (parseInt(item.id) === parseInt(feed_id)) {
        feed = item;
        break;
      }
    }

    if (! feed) {
      return null;
    }

    const { classes } = this.props;
    const {
      user: {
        name,
        id: user_id
      },
      created_at,
      feed_content: content,
      images = [],
      paid_node,
      feed_digg_count: digg_count,
      feed_comment_count: comment_count,
    } = feed;

    return (<Card elevation={0} className={classes.drawer}>
      
      <CardHeader
        avatar={<Avatar>{name[0]}</Avatar>}
        title={`${name} (${user_id})`}
        subheader={created_at}
      />

      <CardContent>{content}</CardContent>

      { images.map(({
        id,
        paid_node
      }) => (<CardMedia key={id}>
        <img
          src={createRequestURI(`files/${id}`)}
          className={classes.drawerImage}
        />
        { paid_node && (<CardHeader
          title={
            paid_node.extra === 'read' ? '查看' : '下载' +
            '收费：' +
            (paid_node.amount * window.FEED.walletRatio / 100)
          }
          className={classes.drawerImageTitle}
        />)}
      </CardMedia>)) }

      <CardContent className={classes.drawerRow}>
        <Chip className={classes.chip} avatar={<Avatar><FavoriteIcon /></Avatar>} label={digg_count} />
        <Chip className={classes.chip} avatar={<Avatar><Forum /></Avatar>} label={comment_count} />
      </CardContent>

    </Card>);

  }

  makeImages(images = []) {
    switch (images.length) {
      case 1:
        const file = images.pop();
        return (<img src={createRequestURI(`files/${file.id}`)} />);

      default:
        return null;
    }
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
