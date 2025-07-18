/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// require jQuery normally
const $ = require('jquery');
// create global $ and jQuery variables
global.$ = global.jQuery = $;

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import './styles/app/container.css';
import './styles/app/pagination.css';
import './styles/app/modal.css';
import './styles/app/form.css';
import './styles/app/error.css';
import './styles/app/success.css';
