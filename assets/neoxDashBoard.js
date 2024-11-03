/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
 import './bootstrap.js';

/* maine styles for customization */
import './css/neoxDashBoard.css'


/* Manage by Asset-Mapper */
import './Helper/sweetAlert.js';
import './Helper/bootstrap.js';
import './Helper/turbo/turbo-helper.js'

// import '/../build/canvas/js/functions.js';

/* Manage by webpack.js */


// import $ from 'jquery';
// // things on "window" become global variables
// window.$ = $;


console.log('This log comes from assets/neoxDashBoard.js - welcome to AssetMapper! 🎉');
