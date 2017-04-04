/**
 * The file is 「feed」component admin manage entry.
 *
 * @author Seven Du <shiweidu@outlook.com>
 */

import React from 'react';
import { render } from 'react-dom';
import injectTapEventPlugin from 'react-tap-event-plugin';
import MuiThemeProvider from 'material-ui/styles/MuiThemeProvider';
import theme from './theme';

// The app entry.
const App = () => (
  <MuiThemeProvider muiTheme={theme}>
    <div>222</div>
  </MuiThemeProvider>
);

document.addEventListener('DOMContentLoaded', () => {

  // Needed for onTouchTap
  // http://stackoverflow.com/a/34015469/988941
  injectTapEventPlugin();

  render(
    <App />,
    document.getElementById('app')
  );
});
