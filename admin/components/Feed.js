/**
 * The file is admin feeds manage page.
 */

import React, { Component } from 'react';
import PropTypes from 'prop-types'

import classnames from 'classnames';
import { withStyles, createStyleSheet } from 'material-ui/styles';
import Grid from 'material-ui/Grid';
import Card, { CardHeader, CardContent, CardMedia, CardActions } from 'material-ui/Card';
import Avatar from 'material-ui/Avatar';
import Button from 'material-ui/Button';
import IconButton from 'material-ui/IconButton';
import Collapse from 'material-ui/transitions/Collapse';

import FavoriteIcon from 'material-ui-icons/Favorite';
// import RemoveRedEye from 'material-ui-icons/RemoveRedEye';
import Forum from 'material-ui-icons/Forum';
import ExpandMoreIcon from 'material-ui-icons/ExpandMore';

import request, { createRequestURI } from '../utils/request';

const FeedStyleSheet = createStyleSheet('FeedStyleSheet', theme => {
  return {
    root: {
      padding: theme.spacing.unit * 2,
    },
    expand: {
      transform: 'rotate(0deg)',
      transition: theme.transitions.create('transform', {
        duration: theme.transitions.duration.shortest,
      }),
    },
    expandOpen: {
      transform: 'rotate(180deg)',
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
  };

  handleExpandClick(feed_id){
    const state = this.state;
    const { feeds = [] } = state;
    this.setState({
      ...state,
      feeds: feeds.map((item) => {
        if (parseInt(item.id) === parseInt(feed_id)) {
          item['expanded'] = ! item['expanded'];
        }

        return item;
      })
    });
  }

  render() {
    const { classes } = this.props;
    const { feeds = [] } = this.state;

    return (
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
                  className={classnames(classes.expand, {
                    [classes.expandOpen]: expanded,
                  })}
                  onTouchTap={() => this.handleExpandClick(id)}
                >
                  <ExpandMoreIcon />
                </IconButton>

              </CardActions>

              <Collapse in={expanded} transitionDuration="auto" unmountOnExit>
                <CardContent>
                  test
                </CardContent>
              </Collapse>

            </Card>
          </Grid>

        )) }

      </Grid>
    );
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
