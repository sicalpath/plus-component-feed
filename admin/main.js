/*
|-----------------------------------------------
| Plus feed-component admin application.
|-----------------------------------------------
|
| The file is entry.
|
| @author Seven Du <shiweidu@outlook.com>
|
*/

import React from 'react';
import { render } from 'react-dom';
import injectTapEventPlugin from 'react-tap-event-plugin';
import { HashRouter as Router } from 'react-router-dom';
import { MuiThemeProvider } from 'material-ui/styles';
import App from 'App';

/**
 * The app entry component.
 *
 * @return {Function}
 * @author Seven Du <shiweidu@outlook.com>
 */
const Main = () => (
  <MuiThemeProvider>
    <Router>
      <App />
    </Router>
  </MuiThemeProvider>
);

document.addEventListener('DOMContentLoaded', () => {

  // Needed for onTouchTap
  // http://stackoverflow.com/a/34015469/988941
  injectTapEventPlugin();

  // Rende app.
  render(
    <Main />,
    document.getElementById('app')
  );

});
