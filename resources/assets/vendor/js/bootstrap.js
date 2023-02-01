import * as bootstrap from 'bootstrap'
import axios from 'axios'

try {
  window.bootstrap = bootstrap
  window.axios = require('axios');
} catch (e) {}

export { bootstrap }
