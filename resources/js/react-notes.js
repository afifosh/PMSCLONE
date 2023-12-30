import NotesApp from "./react-notes-app";
import React from 'react';
import ReactDOM from 'react-dom';

if (document.getElementById('react-notes-app')) {
  ReactDOM.render(<NotesApp />, document.getElementById('react-notes-app'));
}
