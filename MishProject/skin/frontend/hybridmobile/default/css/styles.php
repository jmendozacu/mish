<?php
  header("Content-type: text/css");
  $dir = realpath('');
  $pieces = explode("skin", $dir);
  $x = str_replace("'\'", "/", $pieces[0]);
  require_once($x . '/app/Mage.php'); //Path to Magento
  umask(0);
  Mage::app();

  $primary_color = '#' . Mage::getStoreConfig('hybrid_mobile/general/primary_color', Mage::app()->getStore());
  $primary_light_color = '#' . Mage::getStoreConfig('hybrid_mobile/general/primary_light_color', Mage::app()->getStore());
  $secondary_color = '#' . Mage::getStoreConfig('hybrid_mobile/general/secondary_color', Mage::app()->getStore());
  $light_icons = Mage::getStoreConfig('hybrid_mobile/general/light_icons', Mage::app()->getStore());
?>

@import url(//fonts.googleapis.com/css?family=Roboto:300,400,500);

/* ==========================================================================
   RESET
   ========================================================================== */
html, body, ul, ol, li, dl, dt, dd, fieldset {
  margin: 0;
  padding: 0;
}

body {
  font-family: 'Roboto', sans-serif;
}
* {
  -webkit-touch-callout: none;
  -webkit-user-select: none;
}
input, textarea {
  -webkit-user-select: auto;
}
a {
  cursor: pointer;
  display: inline;
  color: #4c4c4c;
  text-decoration: none;
}
a[disabled="true"] {
  color: #c1cee2;
  text-shadow: none;
}
img {
  border: 0;
}
img, object, embed {
  max-width: 100%;
  height: auto;
}
object, embed {
  height: 100%;
}
img {
  -ms-interpolation-mode: bicubic;
}

/* Forms */
input, select, textarea, button {
  vertical-align: middle;
}
fieldset {
  border: 0;
}
legend {
  display: none;
}
select {
  padding: 0;
}
label {
  clear: both;
}

/* Lists */
ul, ol {
  list-style: none;
}

/* Headings */
h1, h2, h3, h4, h5, h6 {
  margin: 5px 0;
  padding: 0;
  font-weight: normal;
}
/*h1, h2, h3 {
  font-weight: normal;
}*/
.table {
  width: 100%;
}

/*Tools*/
.hide {
  display: none;
}
.text-center {
  text-align: center;
}

/* ==========================================================================
   FOUNDATION GRID
   ========================================================================== */
.row, .row * {
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  box-sizing: border-box;
}
#map_canvas img, #map_canvas embed, #map_canvas object, .map_canvas img, .map_canvas embed, .map_canvas object {
  max-width: none !important;
}
.clearfix {
  *zoom: 1;
}
.clearfix:before, .clearfix:after {
  content: " ";
  display: table;
}
.clearer,
.clearfix:after {
  clear: both;
}
.antialiased {
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}
img {
  display: inline-block;
  vertical-align: middle;
}
.row {
  width: 100%;
  margin-left: auto;
  margin-right: auto;
  margin-top: 0;
  margin-bottom: 0;
}
.row:before, .row:after {
  content: " ";
  display: table;
}
.row:after {
  clear: both;
}
.row.collapse > .column, .row.collapse > .columns {
  padding-left: 0;
  padding-right: 0;
  float: left;
}
.row.collapse .row {
  margin-left: 0;
  margin-right: 0;
}
.row .row {
  width: auto;
  margin-left: -12px;
  margin-right: -12px;
  margin-top: 0;
  margin-bottom: 0;
  max-width: none;
  *zoom: 1;
}
.row .row:before, .row .row:after {
  content: " ";
  display: table;
}
.row .row:after {
  clear: both;
}
.row .row.collapse {
  width: auto;
  margin: 0;
  max-width: none;
  *zoom: 1;
}
.row .row.collapse:before, .row .row.collapse:after {
  content: " ";
  display: table;
}
.row .row.collapse:after {
  clear: both;
}
.column, .columns {
  /* padding-left: 15px;
  padding-right: 15px;
   */
  padding-left: 12px;
  padding-right: 12px;
  width: 100%;
  float: left;
}
@media only screen {
.column.small-centered, .columns.small-centered {
  margin-left: auto;
  margin-right: auto;
  float: none;
}
.column.small-uncentered, .columns.small-uncentered {
  margin-left: 0;
  margin-right: 0;
  float: left;
}
.column.small-uncentered.opposite, .columns.small-uncentered.opposite {
  float: right;
}
.small-push-0 {
  left: 0%;
  right: auto;
}
.small-pull-0 {
  right: 0%;
  left: auto;
}
.small-push-1 {
  left: 8.33333%;
  right: auto;
}
.small-pull-1 {
  right: 8.33333%;
  left: auto;
}
.small-push-2 {
  left: 16.66667%;
  right: auto;
}
.small-pull-2 {
  right: 16.66667%;
  left: auto;
}
.small-push-3 {
  left: 25%;
  right: auto;
}
.small-pull-3 {
  right: 25%;
  left: auto;
}
.small-push-4 {
  left: 33.33333%;
  right: auto;
}
.small-pull-4 {
  right: 33.33333%;
  left: auto;
}
.small-push-5 {
  left: 41.66667%;
  right: auto;
}
.small-pull-5 {
  right: 41.66667%;
  left: auto;
}
.small-push-6 {
  left: 50%;
  right: auto;
}
.small-pull-6 {
  right: 50%;
  left: auto;
}
.small-push-7 {
  left: 58.33333%;
  right: auto;
}
.small-pull-7 {
  right: 58.33333%;
  left: auto;
}
.small-push-8 {
  left: 66.66667%;
  right: auto;
}
.small-pull-8 {
  right: 66.66667%;
  left: auto;
}
.small-push-9 {
  left: 75%;
  right: auto;
}
.small-pull-9 {
  right: 75%;
  left: auto;
}
.small-push-10 {
  left: 83.33333%;
  right: auto;
}
.small-pull-10 {
  right: 83.33333%;
  left: auto;
}
.small-push-11 {
  left: 91.66667%;
  right: auto;
}
.small-pull-11 {
  right: 91.66667%;
  left: auto;
}
.column, .columns {
  position: relative;
  padding-left: 12px;
  padding-right: 12px;
  float: left;
}
.small-1 {
  width: 8.33333%;
}
.small-2 {
  width: 16.66667%;
}
.small-3 {
  width: 25%;
}
.small-4 {
  width: 33.33333%;
}
.small-5 {
  width: 41.66667%;
}
.small-6 {
  width: 50%;
}
.small-7 {
  width: 58.33333%;
}
.small-8 {
  width: 66.66667%;
}
.small-9 {
  width: 75%;
}
.small-10 {
  width: 83.33333%;
}
.small-11 {
  width: 91.66667%;
}
.small-12 {
  width: 100%;
}
[class*="column"] + [class*="column"]:last-child {
  float: left;
}
[class*="column"] + [class*="column"].end {
  float: left;
}
.small-offset-0 {
  margin-left: 0% !important;
}
.small-offset-1 {
  margin-left: 8.33333% !important;
}
.small-offset-2 {
  margin-left: 16.66667% !important;
}
.small-offset-3 {
  margin-left: 25% !important;
}
.small-offset-4 {
  margin-left: 33.33333% !important;
}
.small-offset-5 {
  margin-left: 41.66667% !important;
}
.small-offset-6 {
  margin-left: 50% !important;
}
.small-offset-7 {
  margin-left: 58.33333% !important;
}
.small-offset-8 {
  margin-left: 66.66667% !important;
}
.small-offset-9 {
  margin-left: 75% !important;
}
.small-offset-10 {
  margin-left: 83.33333% !important;
}
.small-offset-11 {
  margin-left: 91.66667% !important;
}
.small-reset-order, .small-reset-order {
  margin-left: 0;
  margin-right: 0;
  left: auto;
  right: auto;
  float: left;
}
}

/* 641px */
@media only screen and (min-width:40.063em) {
.column.medium-centered, .columns.medium-centered {
  margin-left: auto;
  margin-right: auto;
  float: none;
}
.column.medium-uncentered, .columns.medium-uncentered {
  margin-left: 0;
  margin-right: 0;
  float: left;
}
.column.medium-uncentered.opposite, .columns.medium-uncentered.opposite {
  float: right;
}
.medium-push-0 {
  left: 0%;
  right: auto;
}
.medium-pull-0 {
  right: 0%;
  left: auto;
}
.medium-push-1 {
  left: 8.33333%;
  right: auto;
}
.medium-pull-1 {
  right: 8.33333%;
  left: auto;
}
.medium-push-2 {
  left: 16.66667%;
  right: auto;
}
.medium-pull-2 {
  right: 16.66667%;
  left: auto;
}
.medium-push-3 {
  left: 25%;
  right: auto;
}
.medium-pull-3 {
  right: 25%;
  left: auto;
}
.medium-push-4 {
  left: 33.33333%;
  right: auto;
}
.medium-pull-4 {
  right: 33.33333%;
  left: auto;
}
.medium-push-5 {
  left: 41.66667%;
  right: auto;
}
.medium-pull-5 {
  right: 41.66667%;
  left: auto;
}
.medium-push-6 {
  left: 50%;
  right: auto;
}
.medium-pull-6 {
  right: 50%;
  left: auto;
}
.medium-push-7 {
  left: 58.33333%;
  right: auto;
}
.medium-pull-7 {
  right: 58.33333%;
  left: auto;
}
.medium-push-8 {
  left: 66.66667%;
  right: auto;
}
.medium-pull-8 {
  right: 66.66667%;
  left: auto;
}
.medium-push-9 {
  left: 75%;
  right: auto;
}
.medium-pull-9 {
  right: 75%;
  left: auto;
}
.medium-push-10 {
  left: 83.33333%;
  right: auto;
}
.medium-pull-10 {
  right: 83.33333%;
  left: auto;
}
.medium-push-11 {
  left: 91.66667%;
  right: auto;
}
.medium-pull-11 {
  right: 91.66667%;
  left: auto;
}
.column, .columns {
  position: relative;
  padding-left: 15px;
  padding-right: 15px;
  float: left;
}
.medium-1 {
  width: 8.33333%;
}
.medium-2 {
  width: 16.66667%;
}
.medium-3 {
  width: 25%;
}
.medium-4 {
  width: 33.33333%;
}
.medium-5 {
  width: 41.66667%;
}
.medium-6 {
  width: 50%;
}
.medium-7 {
  width: 58.33333%;
}
.medium-8 {
  width: 66.66667%;
}
.medium-9 {
  width: 75%;
}
.medium-10 {
  width: 83.33333%;
}
.medium-11 {
  width: 91.66667%;
}
.medium-12 {
  width: 100%;
}
[class*="column"] + [class*="column"]:last-child {
  float: right;
}
[class*="column"] + [class*="column"].end {
  float: left;
}
.medium-offset-0 {
  margin-left: 0% !important;
}
.medium-offset-1 {
  margin-left: 8.33333% !important;
}
.medium-offset-2 {
  margin-left: 16.66667% !important;
}
.medium-offset-3 {
  margin-left: 25% !important;
}
.medium-offset-4 {
  margin-left: 33.33333% !important;
}
.medium-offset-5 {
  margin-left: 41.66667% !important;
}
.medium-offset-6 {
  margin-left: 50% !important;
}
.medium-offset-7 {
  margin-left: 58.33333% !important;
}
.medium-offset-8 {
  margin-left: 66.66667% !important;
}
.medium-offset-9 {
  margin-left: 75% !important;
}
.medium-offset-10 {
  margin-left: 83.33333% !important;
}
.medium-offset-11 {
  margin-left: 91.66667% !important;
}
.medium-reset-order, .medium-reset-order {
  margin-left: 0;
  margin-right: 0;
  left: auto;
  right: auto;
  float: left;
}
.push-0 {
  left: 0%;
  right: auto;
}
.pull-0 {
  right: 0%;
  left: auto;
}
.push-1 {
  left: 8.33333%;
  right: auto;
}
.pull-1 {
  right: 8.33333%;
  left: auto;
}
.push-2 {
  left: 16.66667%;
  right: auto;
}
.pull-2 {
  right: 16.66667%;
  left: auto;
}
.push-3 {
  left: 25%;
  right: auto;
}
.pull-3 {
  right: 25%;
  left: auto;
}
.push-4 {
  left: 33.33333%;
  right: auto;
}
.pull-4 {
  right: 33.33333%;
  left: auto;
}
.push-5 {
  left: 41.66667%;
  right: auto;
}
.pull-5 {
  right: 41.66667%;
  left: auto;
}
.push-6 {
  left: 50%;
  right: auto;
}
.pull-6 {
  right: 50%;
  left: auto;
}
.push-7 {
  left: 58.33333%;
  right: auto;
}
.pull-7 {
  right: 58.33333%;
  left: auto;
}
.push-8 {
  left: 66.66667%;
  right: auto;
}
.pull-8 {
  right: 66.66667%;
  left: auto;
}
.push-9 {
  left: 75%;
  right: auto;
}
.pull-9 {
  right: 75%;
  left: auto;
}
.push-10 {
  left: 83.33333%;
  right: auto;
}
.pull-10 {
  right: 83.33333%;
  left: auto;
}
.push-11 {
  left: 91.66667%;
  right: auto;
}
.pull-11 {
  right: 91.66667%;
  left: auto;
}
}

/* 1024px Ipad */
@media only screen and (min-width:64.063em) {
.column.large-centered, .columns.large-centered {
  margin-left: auto;
  margin-right: auto;
  float: none;
}
.column.large-uncentered, .columns.large-uncentered {
  margin-left: 0;
  margin-right: 0;
  float: left;
}
.column.large-uncentered.opposite, .columns.large-uncentered.opposite {
  float: right;
}
.large-push-0 {
  left: 0%;
  right: auto;
}
.large-pull-0 {
  right: 0%;
  left: auto;
}
.large-push-1 {
  left: 8.33333%;
  right: auto;
}
.large-pull-1 {
  right: 8.33333%;
  left: auto;
}
.large-push-2 {
  left: 16.66667%;
  right: auto;
}
.large-pull-2 {
  right: 16.66667%;
  left: auto;
}
.large-push-3 {
  left: 25%;
  right: auto;
}
.large-pull-3 {
  right: 25%;
  left: auto;
}
.large-push-4 {
  left: 33.33333%;
  right: auto;
}
.large-pull-4 {
  right: 33.33333%;
  left: auto;
}
.large-push-5 {
  left: 41.66667%;
  right: auto;
}
.large-pull-5 {
  right: 41.66667%;
  left: auto;
}
.large-push-6 {
  left: 50%;
  right: auto;
}
.large-pull-6 {
  right: 50%;
  left: auto;
}
.large-push-7 {
  left: 58.33333%;
  right: auto;
}
.large-pull-7 {
  right: 58.33333%;
  left: auto;
}
.large-push-8 {
  left: 66.66667%;
  right: auto;
}
.large-pull-8 {
  right: 66.66667%;
  left: auto;
}
.large-push-9 {
  left: 75%;
  right: auto;
}
.large-pull-9 {
  right: 75%;
  left: auto;
}
.large-push-10 {
  left: 83.33333%;
  right: auto;
}
.large-pull-10 {
  right: 83.33333%;
  left: auto;
}
.large-push-11 {
  left: 91.66667%;
  right: auto;
}
.large-pull-11 {
  right: 91.66667%;
  left: auto;
}
.column, .columns {
  position: relative;
  padding-left: 15px;
  padding-right: 15px;
  float: left;
}
.large-1 {
  width: 8.33333%;
}
.large-2 {
  width: 16.66667%;
}
.large-3 {
  width: 25%;
}
.large-4 {
  width: 33.33333%;
}
.large-5 {
  width: 41.66667%;
}
.large-6 {
  width: 50%;
}
.large-7 {
  width: 58.33333%;
}
.large-8 {
  width: 66.66667%;
}
.large-9 {
  width: 75%;
}
.large-10 {
  width: 83.33333%;
}
.large-11 {
  width: 91.66667%;
}
.large-12 {
  width: 100%;
}
[class*="column"] + [class*="column"]:last-child {
  float: right;
}
[class*="column"] + [class*="column"].end {
  float: left;
}
.large-offset-0 {
  margin-left: 0% !important;
}
.large-offset-1 {
  margin-left: 8.33333% !important;
}
.large-offset-2 {
  margin-left: 16.66667% !important;
}
.large-offset-3 {
  margin-left: 25% !important;
}
.large-offset-4 {
  margin-left: 33.33333% !important;
}
.large-offset-5 {
  margin-left: 41.66667% !important;
}
.large-offset-6 {
  margin-left: 50% !important;
}
.large-offset-7 {
  margin-left: 58.33333% !important;
}
.large-offset-8 {
  margin-left: 66.66667% !important;
}
.large-offset-9 {
  margin-left: 75% !important;
}
.large-offset-10 {
  margin-left: 83.33333% !important;
}
.large-offset-11 {
  margin-left: 91.66667% !important;
}
.large-reset-order, .large-reset-order {
  margin-left: 0;
  margin-right: 0;
  left: auto;
  right: auto;
  float: left;
}
.push-0 {
  left: 0%;
  right: auto;
}
.pull-0 {
  right: 0%;
  left: auto;
}
.push-1 {
  left: 8.33333%;
  right: auto;
}
.pull-1 {
  right: 8.33333%;
  left: auto;
}
.push-2 {
  left: 16.66667%;
  right: auto;
}
.pull-2 {
  right: 16.66667%;
  left: auto;
}
.push-3 {
  left: 25%;
  right: auto;
}
.pull-3 {
  right: 25%;
  left: auto;
}
.push-4 {
  left: 33.33333%;
  right: auto;
}
.pull-4 {
  right: 33.33333%;
  left: auto;
}
.push-5 {
  left: 41.66667%;
  right: auto;
}
.pull-5 {
  right: 41.66667%;
  left: auto;
}
.push-6 {
  left: 50%;
  right: auto;
}
.pull-6 {
  right: 50%;
  left: auto;
}
.push-7 {
  left: 58.33333%;
  right: auto;
}
.pull-7 {
  right: 58.33333%;
  left: auto;
}
.push-8 {
  left: 66.66667%;
  right: auto;
}
.pull-8 {
  right: 66.66667%;
  left: auto;
}
.push-9 {
  left: 75%;
  right: auto;
}
.pull-9 {
  right: 75%;
  left: auto;
}
.push-10 {
  left: 83.33333%;
  right: auto;
}
.pull-10 {
  right: 83.33333%;
  left: auto;
}
.push-11 {
  left: 91.66667%;
  right: auto;
}
.pull-11 {
  right: 91.66667%;
  left: auto;
}
}

/* ==========================================================================
   STYLE
   ========================================================================== */
.inner-head {
  padding: 3px 5px;
  background: #ddd;
  text-align: right;
  line-height: 1.25em;
}
.inner-head h1, .inner-head h2, .inner-head h3, .inner-head h4, .inner-head h5 {
  margin: 0;
  float: left;
}
.page-head h1, .category-head h2 {
  color: #7d90ae;
  font-size: 13px;
  margin: 7px 0;
}
.category-head {
  font-size: 11px;
}
.page-head h1, .category-head h2 {
  color: #7d90ae;
  font-size: 13px;
  margin: 7px 0;
}
.category-head {
  font-size: 11px;
}

/* Tools */
.clear {
  clear: both;
}
.a-left {
  text-align: left !important;
}
.a-center {
  text-align: center !important;
}
.a-right {
  text-align: right !important;
}
.f-left {
  float: left !important;
}
.f-right {
  float: right !important;
}
.v-top {
  vertical-align: top !important;
}
.v-middle {
  vertical-align: middle !important;
}
.no-display {
  display: none;
}
.nowrap, .nobr {
  white-space: nowrap !important;
}
.bg-gray {
  background: #f7f7f7 !important;
}
.no-margin {
  margin: 0 !important;
}
.no-padding {
  padding: 0 !important;
}
.alpha {
  padding-left: 0 !important;
}
.omega {
  padding-right: 0 !important;
}

/* ==========================================================================
  PAGE DIVIDER
   ========================================================================== */
.page-divider {
  background: #f7f7f7;
  padding: 10px 0;
  text-align: center;
}
.page-divider.d-stretch {
  margin: 0 -12px;
}
.page-divider h1 {
  margin: 0;
  font-size: 15px;
  text-transform: uppercase;
  line-height: 20px;
  color: #949494;
}

/* ==========================================================================
  BUTTONS
   ========================================================================== */
button, .button, a.button {
  border: 1px solid <?=$primary_color?>;
  cursor: pointer;
  font-weight: normal;
  line-height: normal;
  margin: 0 0 12px;
  padding: 13px 0 !important;
  text-transform: uppercase;
  position: relative;
  text-decoration: none;
  text-align: center;
  display: inline-block !important;
  font-size: 17px !important;
  background-color: #ffffff;
  color: <?=$primary_color?>;
  -webkit-appearance: none;
  font-weight: normal !important;
  width: 100%;
}
button:active, button:focus,
.button:active, .button:focus,
a.button:active {
  background-color: <?=$primary_color?>;
  border-color: <?=$primary_color?>;
  color: #fff;
}
/* Prominent */
.opc .button,
.opc .button1,
button.prominent,
.button.prominent,
a.button.prominent {
  background-color: <?=$secondary_color?>;
  color: #fff;
  border-color: <?=$secondary_color?>;
}
.opc .button:active,
.opc .button1:active,
button.prominent:active,
.button.prominent:active,
a.button.prominent:active {
  background-color: <?=$primary_color?>;
  border-color: <?=$primary_color?>;
}
.button.position-fixed-bottom {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  margin: 0;
  z-index: 25;
}
.button.position-fixed-top {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  margin: 0;
  z-index: 25;
}

button.large, .button.large {
  padding: 16px 0 !important;
  font-size: 20px !important;
}
button.small, .button.small {
  padding-top: 0.875rem;
  padding-right: 1.75rem;
  padding-bottom: 0.9375rem;
  padding-left: 1.75rem;
  font-size: 0.8125rem;
}
button.tiny, .button.tiny {
  padding-top: 0.625rem;
  padding-right: 1.25rem;
  padding-bottom: 0.6875rem;
  padding-left: 1.25rem;
  font-size: 0.6875rem;
}
button.expand, .button.expand {
  padding-right: 0;
  padding-left: 0;
  width: 100%;
}
button.left-align, .button.left-align {
  text-align: left;
  text-indent: 0.75rem;
}
button.right-align, .button.right-align {
  text-align: right;
  padding-right: 0.75rem;
}
button.radius, .button.radius {
  -webkit-border-radius: 3px;
  border-radius: 3px;
}
button.round, .button.round {
  -webkit-border-radius: 1000px;
  border-radius: 1000px;
}
button.disabled, button[disabled], .button.disabled, .button[disabled] {
  background-color: <?=$primary_color?>;
  border-color: <?=$primary_color?>;
  color: white;
  cursor: default;
  opacity: 0.4;
  -webkit-box-shadow: none;
  box-shadow: none;
}
button.disabled:hover, button.disabled:focus, button[disabled]:hover, button[disabled]:focus, .button.disabled:hover, .button.disabled:focus, .button[disabled]:hover, .button[disabled]:focus {
  background-color: #6d375a;
}
button.disabled:hover, button.disabled:focus, button[disabled]:hover, button[disabled]:focus, .button.disabled:hover, .button.disabled:focus, .button[disabled]:hover, .button[disabled]:focus {
  color: white;
}
button.disabled:hover, button.disabled:focus, button[disabled]:hover, button[disabled]:focus, .button.disabled:hover, .button.disabled:focus, .button[disabled]:hover, .button[disabled]:focus {
  background-color: <?=$primary_color?>;
}
button.disabled.secondary, button[disabled].secondary, .button.disabled.secondary, .button[disabled].secondary {
  background-color: <?=$primary_color?>;
  border-color: #6d375a;
  color: white;
  cursor: default;
  opacity: 0.7;
  -webkit-box-shadow: none;
  box-shadow: none;
}
button.disabled.secondary:hover, button.disabled.secondary:focus, button[disabled].secondary:hover, button[disabled].secondary:focus, .button.disabled.secondary:hover, .button.disabled.secondary:focus, .button[disabled].secondary:hover, .button[disabled].secondary:focus {
  background-color: #6d375a;
}
button.disabled.secondary:hover, button.disabled.secondary:focus, button[disabled].secondary:hover, button[disabled].secondary:focus, .button.disabled.secondary:hover, .button.disabled.secondary:focus, .button[disabled].secondary:hover, .button[disabled].secondary:focus {
  color: white;
}
button.disabled.secondary:hover, button.disabled.secondary:focus, button[disabled].secondary:hover, button[disabled].secondary:focus, .button.disabled.secondary:hover, .button.disabled.secondary:focus, .button[disabled].secondary:hover, .button[disabled].secondary:focus {
  background-color: <?=$primary_color?>;
}
button.disabled.success, button[disabled].success, .button.disabled.success, .button[disabled].success {
  background-color: #5da423;
  border-color: #4a831c;
  color: white;
  cursor: default;
  opacity: 0.7;
  -webkit-box-shadow: none;
  box-shadow: none;
}
button.disabled.success:hover, button.disabled.success:focus, button[disabled].success:hover, button[disabled].success:focus, .button.disabled.success:hover, .button.disabled.success:focus, .button[disabled].success:hover, .button[disabled].success:focus {
  background-color: #4a831c;
}
button.disabled.success:hover, button.disabled.success:focus, button[disabled].success:hover, button[disabled].success:focus, .button.disabled.success:hover, .button.disabled.success:focus, .button[disabled].success:hover, .button[disabled].success:focus {
  color: white;
}
button.disabled.success:hover, button.disabled.success:focus, button[disabled].success:hover, button[disabled].success:focus, .button.disabled.success:hover, .button.disabled.success:focus, .button[disabled].success:hover, .button[disabled].success:focus {
  background-color: #5da423;
}
button.disabled.alert, button[disabled].alert, .button.disabled.alert, .button[disabled].alert {
  background-color: #c60f13;
  border-color: #9e0c0f;
  color: white;
  cursor: default;
  opacity: 0.7;
  -webkit-box-shadow: none;
  box-shadow: none;
}
button.disabled.alert:hover, button.disabled.alert:focus, button[disabled].alert:hover, button[disabled].alert:focus, .button.disabled.alert:hover, .button.disabled.alert:focus, .button[disabled].alert:hover, .button[disabled].alert:focus {
  background-color: #9e0c0f;
}
button.disabled.alert:hover, button.disabled.alert:focus, button[disabled].alert:hover, button[disabled].alert:focus, .button.disabled.alert:hover, .button.disabled.alert:focus, .button[disabled].alert:hover, .button[disabled].alert:focus {
  color: white;
}
button.disabled.alert:hover, button.disabled.alert:focus, button[disabled].alert:hover, button[disabled].alert:focus, .button.disabled.alert:hover, .button.disabled.alert:focus, .button[disabled].alert:hover, .button[disabled].alert:focus {
  background-color: #c60f13;
}
@media only screen and (min-width:40.063em) {
  button, .button {
    display: inline-block;
  }
}

/* ==========================================================================
   FORMS
   ========================================================================== */
.input-box textarea,
textarea.input-text {
  border-width: 0 0 1px;
  border-color: -moz-use-text-color -moz-use-text-color #C8C8C8;
  padding: 12px 0 0;
}


/* ==========================================================================
   CATEGORY MENU
   ========================================================================== */

.category-menu {
    position: fixed;
    z-index: 30;
    left: 0;
    top: 0;
    width: 100%;
    padding-top: 48px;
    overflow: hidden;
    /*height: calc(100% - 48px);*/
    height: 100%;
    box-sizing: border-box;
    background: rgba(255, 255, 255, 1);
    transition: transform 0.4s cubic-bezier(0.080, 0.405, 0.150, 0.985);
    transition: -webkit-transform 0.4s cubic-bezier(0.080, 0.405, 0.150, 0.985);
    /* From Top */
    transform: translate3d(0, -100%, 0);
    -webkit-transform: translate3d(0, -100%, 0);
    /* From Left */
    /*transform: translate3d(-100%, 0, 0);*/
    /*-webkit-transform: translate3d(-100%, 0, 0);*/
}
.category-menu.active {
    transform: translate3d(0, 0, 0);
    -webkit-transform: translate3d(0, 0, 0);
}
.category-menu .menu-overlay {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    /*height: 100%;*/
    height: 20000px;
    z-index: 2;
    background: rgba(0, 0, 0, 0.72);
    visibility: hidden;
    opacity: 0;
    transition-timing-function: ease-in;
    transition-property: opacity, visibility;
    transition-duration: 0.28s;
}
.category-menu .menu-overlay.active {
    transition-timing-function: ease-out;
    visibility: visible;
    opacity: 1;
}
.category-menu .swiper-container {
    height: 100%;
    overflow: visible;
}
.category-menu .menu-layer {
    position: absolute;
    z-index: 4;
    right: 0;
    top: 0;
    height: 100%;
    width: calc(100% - 100px);
    background-color: #fff;
    transition-timing-function: cubic-bezier(.58,.36,.97,.62);
    transition-property: transform, -webkit-transform;
    transition-duration: 0.22s;
    transform: translate3d(100%, 0, 0);
    -webkit-transform: translate3d(100%, 0, 0);
}
/* full width layer longer transition */
.category-menu .menu-layer .menu-layer {
    transition-duration: 0.26s;
}
.category-menu .menu-layer .menu-layer.active {
    transition-duration: 0.38s;
}
.category-menu > .swiper-root > .swiper-wrapper > .menu-layer > .swiper-container > .swiper-wrapper > .menu-layer {
    width: calc(100% + 100px);
}
.category-menu .menu-layer.swiper-deep .menu-layer {
    width: 100%;
}
.category-menu .menu-layer.active {
    transition-duration: 0.3s;
    transition-timing-function: cubic-bezier(.21,.75,.39,.99);
    transform: translate3d(0, 0, 0);
    -webkit-transform: translate3d(0, 0, 0);
}
.category-menu .swiper-slide {
    height: 64px;
}
.category-menu .swiper-slide.active {
    z-index: 3;
}
.category-menu .menu-item {
    display: block;
    height: 64px;
    line-height: 64px;
    text-transform: uppercase;
    position: relative;
    font-size: 14px;
    padding: 0 20px;
    background-color: #fff;
    text-overflow: ellipsis;
    overflow: hidden;
    white-space: nowrap;
    transition: background-color 0.4s;
}
.category-menu .menu-item i {
    float: right;
    font-size: 26px;
    color: #8f8f8f;
    margin-top: 18px;
}
.category-menu .menu-item:active {
    background-color: #f0f0f0;
    transition: none;
}
.category-menu .menu-item::after {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    height: 100%;
    width: 4px;
    background-color: <?=$primary_color?>;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.4s, visibility 0.4s;
}
.category-menu .menu-layer .menu-item::after {
    display: none;
}
/*.category-menu .active .menu-item {
    background-color: #fff;
}*/
.category-menu .active .menu-item::after {
    visibility: visible;
    opacity: 1;
}
.category-menu .menu-item.with-image {
    padding-left: 120px;
}
.category-menu .menu-item .menu-item-image {
    width: 100px;
    height: 100%;
    position: absolute;
    left: 0;
    top: 0;
    background-position: center center;
    background-repeat: no-repeat;
    background-size: cover;
}
/* End Category Menu */


/* Content */
#main {
  margin: 0;
  padding: 0;
}
p {
  padding: 0;
  margin: 5px 0;
}
.main-container {
  padding: 0 5px 5px;
}
.ui-li-desc {
  font-size: 13px !important;
  margin: 0 0 .6em !important;
}
label.ui-input-text {
  font-size: 16px !important;
}
.page-title {
  background: transparent;
  padding: 18px 16px 10px;
}
.page-title h1,
.page-title .ui-title {
  text-transform: capitalize;
  font-size: 20px;
  text-align: left;
  margin: 0;
  /*font-weight: 500;*/
}
.page-title h1 a {
    font-size: 16px;
    color: <?php echo $primary_color ?>;
    vertical-align: baseline;
    font-weight: 300;
}
.ui-bar-a {
  border-left: 0 !important;
  border-right: 0 !important;
}
.topbannerbar {
  height: 38px;
  padding: 5px 0 0;
}
.topbannerbar .backbutton.first-time {
  display: none;
}
.topbannerbar .backbutton {
  margin-top: 2px !important;
}
.topbannerbar .searchbar {
  margin: 0 10px 0 85px;
}
.topbannerbar .searchbar .ui-btn-inner {
  padding: 0;
}
.topbannerbar .searchbar.first-time {
  margin: 0 10px;
}
.form-language label {
  display: none !important;
}

/* ==========================================================================
  CALL TO ACTION
   ========================================================================== */
.call-to-action {
  display: block;
  color: #ba93ac;
  text-align: center;
  border: 1px solid #e5d6e0;
  margin: 12px 0;
  padding: 0.5em 0;
  background: #fff;
}
.call-to-action:active {
  background: #c2c2c2;
  border: 1px solid #aea3aa;
  color: #8e7083;
}
.call-to-action.cta-home h1 {
  font-size: 18px;
  font-size: 18px;
  margin-bottom: 0;
  text-transform: uppercase;
}
.call-to-action.cta-home p {
  font-size: 14px;
  font-size: 13px;
  font-weight: 500;
}

/* ==========================================================================
  PRODUCT GRIDS
   ========================================================================== */

.category-products-grid {
   padding: 0 8px;
}
.category-products-grid .grid-block {
  padding-left: 8px;
  padding-right: 8px;
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  box-sizing: border-box;
  background-color: #ffffff;
  text-align: center;
  margin-bottom: .5rem;
}

.catalog-category-view .category-products-grid .grid-block:nth-child(2n+1){
   clear:left;
}

.category-products-grid .grid-block:active {
  background-color: #e0e0e0;
}
.category-products-grid .grid-block-image a {
  display: inline-block;
  -webkit-transition: background-color 1.5s;
  -moz-transition: background-color 1.5s;
  -ms-transition: background-color 1.5s;
  -o-transition: background-color 1.5s;
  transition: background-color 1.5s;
}
/*.category-products-grid .product-link.s1 {
  background-color: #6F3F46;
}
.category-products-grid .product-link.s2 {
  background-color: #AF636D;
}
.category-products-grid .product-link.s3 {
  background-color: #FC8E9D;
}
.category-products-grid .product-link.s4 {
  background-color: #D57985;
}
.category-products-grid .product-link.s5 {
  background-color: #98565E;
}*/
.category-products-grid .grid-block .info-sec {
  min-height: 84px;
  text-align: left;
  padding: 0;
}
.category-products-grid .grid-block .product-name {
  font-weight: 500;
  font-size: 13px;
  color: #2c2c2c;
  font-weight: normal;
  min-height: 34px;
}
.category-products-grid .grid-block .price-box {
  font-size: 15px;
  text-shadow: none;
}
.category-products-grid .grid-block img {
  width: 100%;
  opacity: 0;
}
.category-products-grid .price {
  font-size: 15px;
}
.category-products-grid .special-price {
  margin: 0;
}
.category-products-grid .special-price .price:before {
  content: 'SALE ';
  color: <?=$secondary_color?>;
}
.category-products-grid .old-price .price {
  font-size: 13px;
}
.category-products-grid .old-price {
  margin: 0 9px 0 0;
}

/* ==========================================================================
  FEATURED CATEGORIES
   ========================================================================== */
#sbc {
    position: relative;
    /*height: 118px;*/
    /*font-size: 0;*/
}
#sbc::after {
    content: '';
    position: absolute;
    left: 16px;
    right: 16px;
    bottom: 0;
    border-top: 1px solid #dfdfdf;
}
#sbc .swiper-wrapper {
    min-width: 100%;
}
#sbc .swiper-slide {
    /*padding: 0 6px;*/
    /*width: 120px;*/
    width: 38%;
}
#sbc .swiper-slide:first-child {
    padding-left: 16px;
}
#sbc .swiper-slide:last-child {
    padding-right: 16px;
}
#sbc .swiper-slide .sbc-image {
    display: inline-block;
    vertical-align: bottom;
    width: 120px;
    height: 76px;
    background-repeat: no-repeat;
    background-position: center center;
    background-size: cover;
    background-color: #aaa;
}
#sbc .swiper-slide h4 {
    font-size: 14px;
    margin: 10px 0 12px;
    font-weight: 300;
}

.featured-category {
  position: relative;
  margin-bottom: 12px;
}
.featured-category h1 {
  font-size: 18px;
  padding: 0 14px;
  text-shadow: none;
  color: #ffffff;
  position: absolute;
  bottom: 8px;
  width: 100%;
  z-index: 2;
}
.featured-category .ui-link {
  position: relative;
  background-repeat: no-repeat;
  background-position: center center;
  background-size: cover;
  display: block;
  height: 66px;
}
.featured-category .ui-link:after {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: #2c2c2c;
  opacity: 0.6;
  z-index: 1;
}
.featured-category a:active h1 {
  opacity: .4;
}
.featured-category h1 i {
  font-size: 22px;
}
.featured-categories {
  margin-top: 12px;
}

/* ======================================================================================= */
/* Layout ================================================================================ */

/* All */
.col2-set, .col3-set, .col4-set, .col5-set {
  clear: both;
}

/* Content columns */
.col2-set .col-1, .col2-set .col-2 {
  width: 48.5%;
}
.col2-set .col-1 {
  float: left;
}
.col2-set .col-2 {
  float: right;
}

/* Content alternative columns */
.col2-alt-set .col-1 {
  width: 32%;
}
.col2-alt-set .col-2 {
  width: 65%;
}
.col2-alt-set .col-1 {
  float: left;
}
.col2-alt-set .col-2 {
  float: right;
}

/* Page paddings */
/*#contactForm, */
/* #mobileproductview, */
.cms-page-view {
  padding: 0 10px;
}
.page-sitemap .page-title, .cms-page-view .page-title {
  margin: 0 -10px;
}
.tags-list, .page-sitemap, .customer-account-logoutsuccess {
  padding: 0 10px 15px;
}
.buttons-set {
  clear: both;
  margin-top: 10px;
}
#mobileproductview #quick-info, #mobileproductview #product-tabs {
  font-size: 14px;
  color: #333;
}

/* ==========================================================================
  jQuery UI overrides
   ========================================================================== */
.ui-overlay-a, .ui-page-theme-a, .ui-page-theme-a .ui-panel-wrapper {
  background: #fff;
  text-shadow: none;
  color: #3a3a3a;
}
.ui-page-theme-a .ui-btn:focus, html .ui-bar-a .ui-btn:focus, html .ui-body-a .ui-btn:focus, html body .ui-group-theme-a .ui-btn:focus, html head + body .ui-btn.ui-btn-a:focus, .ui-page-theme-a .ui-focus, html .ui-bar-a .ui-focus, html .ui-body-a .ui-focus, html body .ui-group-theme-a .ui-focus, html head + body .ui-btn-a.ui-focus, html head + body .ui-body-a.ui-focus {
  box-shadow: none;
}
.ui-bar-a, .ui-page-theme-a .ui-bar-inherit, html .ui-bar-a .ui-bar-inherit, html .ui-body-a .ui-bar-inherit, html body .ui-group-theme-a .ui-bar-inherit {
  background-color: #f7f7f7;
  border: 0 none;
}
.ui-page-theme-a .ui-btn, html .ui-bar-a .ui-btn, html .ui-body-a .ui-btn, html body .ui-group-theme-a .ui-btn, html head + body .ui-btn.ui-btn-a, .ui-page-theme-a .ui-btn:visited, html .ui-bar-a .ui-btn:visited, html .ui-body-a .ui-btn:visited, html body .ui-group-theme-a .ui-btn:visited, html head + body .ui-btn.ui-btn-a:visited {
  /*background-color: #fff;*/
  text-shadow: none;
  text-transform: uppercase;
}
/*.ui-page-theme-a .ui-btn:hover, html .ui-bar-a .ui-btn:hover, html .ui-body-a .ui-btn:hover, html body .ui-group-theme-a .ui-btn:hover, html head + body .ui-btn.ui-btn-a:hover {
  background-color: #fff;
  text-shadow: none;
  border-color: <?=$primary_color?>;
}*/
.ui-page-theme-a .ui-radio-on:after, html .ui-bar-a .ui-radio-on:after, html .ui-body-a .ui-radio-on:after, html body .ui-group-theme-a .ui-radio-on:after, .ui-btn.ui-radio-on.ui-btn-a:after,
.ui-page-theme-a .ui-checkbox-on:after, html .ui-bar-a .ui-checkbox-on:after, html .ui-body-a .ui-checkbox-on:after, html body .ui-group-theme-a .ui-checkbox-on:after, .ui-btn.ui-checkbox-on.ui-btn-a:after, .ui-page-theme-a .ui-slider-track .ui-btn-active, html .ui-bar-a .ui-slider-track .ui-btn-active, html .ui-body-a .ui-slider-track .ui-btn-active, html body .ui-group-theme-a .ui-slider-track .ui-btn-active, html body div.ui-slider-track.ui-body-a .ui-btn-active {
  background-color: <?=$primary_color?>;
  border-color: <?=$primary_color?>;
}
.ui-checkbox .ui-btn,
.ui-radio .ui-btn {
  border-color: <?=$primary_color?>;
  color: <?=$primary_color?>;
}
.ui-checkbox .ui-btn:active,
.ui-radio .ui-btn:active {
    background-color: <?=$primary_color?>;
    color: #fff;
}
.ui-btn, label.ui-btn {
  font-weight: normal;
}
.ui-select .ui-btn {
  border-color: <?=$primary_color?>;
}
.ui-select .ui-btn:active {
/*.ui-page-theme-a .ui-btn:active, html .ui-bar-a .ui-btn:active, html .ui-body-a .ui-btn:active, html body .ui-group-theme-a .ui-btn:active, html head + body .ui-btn.ui-btn-a:active {*/
  background-color: <?=$primary_color?>;
  border-color: <?=$primary_color?>;
  color: #fff;
  text-shadow: none;
}
.ui-select .ui-btn span {
  color: <?=$primary_color?>;
}
.ui-select .ui-btn:active span {
  color: #fff;
}
.ui-btn-corner-all, .ui-btn.ui-corner-all, .ui-slider-track.ui-corner-all, .ui-flipswitch.ui-corner-all, .ui-li-count {
  border-radius: 0;
}
.ui-shadow {
  box-shadow: none;
}
.ui-collapsible-content {
    padding: 16px;
}

/* ======================================================================================= */
/* Header */
.header {

}
.header .header-top {
  text-align: center;
  height: 48px;
  line-height: 48px;
  overflow: hidden;
  background: <?=$primary_color?>;
  position: relative;
  z-index: 50;
}
.header .header-wrapper {
  text-align: center;
}
.header .header-wrapper .icon-link {
  font-size: 23px;
  vertical-align: middle;
  color: <?=($light_icons) ? '#ffffff' : '#000'?>;
  display: inline-block;
  width: 48px;
  overflow: hidden;
  transform: translate3d(0, 0, 0);
  -webkit-transform: translate3d(0, 0, 0);
  transition: width 0.4s;
}
.header .header-wrapper .icon-link.off {
    width: 0;
}
.header .header-wrapper .left-block,
.header .header-wrapper .right-block {
  position: absolute;
  top: 0;
  bottom: 0;
  font-size: 0;
}
.header .header-wrapper .left-block {
  left: 0;
}
.header .header-wrapper .right-block {
  right: 0;
}
.header .header-wrapper .center-block {
  display: inline-block;
}
.header .header-wrapper .icon-link:active {
  background: <?=$secondary_color?>;
}
.header .header-top .logo {
  display: inline-block;
  width: 110px;
}
.header .header-top .logo img {
  max-width: 100%;
  max-height: 100%;
  vertical-align: middle;
}
.header .header-top .logo img:active {
  opacity: 0.7;
}
.header .logo-text {
  margin: 0;
  font-size: 16px;
  color: #ffffff;
  width: 110px;
  display: inline-block;
  white-space: nowrap;
  text-overflow: ellipsis;
  overflow: hidden;
  text-transform: capitalize !important;
}
/*.category-title .page-title {
  background: #f4f4f4;
  border: none;
}*/
.header .searchbar {
  position: absolute;
  left: 0;
  top: 0;
  width: 100%;
  z-index: 49;
  border-bottom: 1px solid <?=$primary_color?>;
  -webkit-transition: all .25s;
  transition: all .25s;
}
.header .searchbar.animate {
  -webkit-transform: translate3d(0, 100%, 0);
  transform: translate3d(0, 100%, 0);
}
.header .searchbar #search {
  height: 46px;
  line-height: 46px;
  border: 0 none;
}

.catalogsearch-advanced-index .breadcrumbs, .catalogsearch-advanced-result .breadcrumbs {
  display: none;
}
.catalogsearch-advanced-index .page-title {
  margin-top: 0;
}
.catalogsearch-advanced-index ul.form-list {
  padding: 10px 5px 10px 10px;
}
.catalogsearch-result-index .category-products-grid .grid-block:nth-child(2n+1){
   clear:left;
}
.advanced-search-amount, .advanced-search-summary {
  padding: 10px 10px 0;
  font-size: 13px;
}
.category-products .ui-content {
  overflow: hidden !important;
}
.header-top .welcome {
  margin: 5px 5px 0 0;
}
.form-language .ui-select .ui-btn-icon-right .ui-btn-inner {
  font-size: 14px !important;
  padding: .4em 38px .4em 15px !important;
}
.header-top .links, .header-top .links li, .header-top .welcome-msg {
  display: inline;
  font-size: 12px;
}
.header-top .links a {
  color: #FFF;
}
.header-top .welcome-msg {
  color: #FFF;
}
.header-top .col-2 {
  padding: 7px 7px 0 0;
  text-align: right;
}
.shop-access {
  clear: both;
}
.shop-access ul, .informational ul {
  list-style: none;
  padding: 0;
  margin: 0;
  font-size: 11px;
  font-weight: 500;
}
.shop-access ul {
  overflow: hidden;
}
.shop-access ul li {
  display: inline;
  padding: 0 5px;
  text-align: center;
}
.shop-access ul li a {
  line-height: 1.75;
  text-decoration: none;
  font-size: 15px;
  color: #fff;
}
.switch-language {
  float: right;
  margin-bottom: 5px;
}
.switch-language select {
  font-size: 12px;
}
.switch-language label {
  display: none;
}

/* Menu */
ul#mobile-menu {
  margin: 0 !important;
}
ul#mobile-menu li.level4 a:before {
  padding-left: 5px !important;
  padding-right: 5px;
  content: "\2D \ ";
}
ul#mobile-menu li.level5 a:before {
  padding-left: 20px !important;
  padding-right: 5px;
  content: "\2D \ ";
}
ul#mobile-menu li.level6 a:before {
  padding-left: 35px !important;
  padding-right: 5px;
  content: "\2D \ ";
}
ul#mobile-menu .numcount {
  position: absolute;
  font-size: 11px;
  font-weight: 500;
  padding: .2em .5em;
  top: 50%;
  margin-top: -.9em;
  right: 38px;
}

/* Acoordion Menu */
ul#mobile-menu.accordion {
  margin: 0 8px !important;
}
ul#mobile-menu.accordion .ui-corner-top {
  border-top-left-radius: 0;
  -moz-border-radius-topleft: 0;
  -webkit-border-top-left-radius: 0;
  border-top-right-radius: 0;
  -moz-border-radius-topright: 0;
  -webkit-border-top-right-radius: 0;
}
ul#mobile-menu.accordion .ui-corner-bottom {
  border-bottom-left-radius: 0;
  -moz-border-radius-bottomleft: 0;
  -webkit-border-bottom-left-radius: 0;
  border-bottom-right-radius: 0;
  -moz-border-radius-bottomright: 0;
  -webkit-border-bottom-right-radius: 0;
}
ul#mobile-menu.accordion .ui-collapsible-heading {
  margin: 0;
}
ul#mobile-menu.accordion .ui-collapsible-heading a {
  margin: 0 -8px;
}
ul#mobile-menu.accordion .ui-collapsible-heading .ui-btn-text {
  padding-left: 8px;
}
ul#mobile-menu.accordion .ui-collapsible-heading .ui-btn-icon-right .ui-btn-inner {
  padding-left: 4px;
  padding-right: 32px;
}
ul#mobile-menu.accordion .ui-collapsible-heading .ui-btn-icon-right .ui-btn-inner .ui-icon {
  margin-right: 0;
}
ul#mobile-menu.accordion .ui-collapsible-heading .numcount {
  position: static;
  font-size: 11px;
  font-weight: 500;
  padding: .2em .5em;
  top: 50%;
  margin: 0 5px 0;
  float: right;
}
ul#mobile-menu.accordion .ui-collapsible {
  margin: 0;
  padding: 0;
}
ul#mobile-menu.accordion .ui-collapsible-content {
  margin: 0 -8px;
  padding: 0;
}

/* Navigation */
#nav {
  font: bold 14px/18px Helvetica;
}
#nav ul li {
  font-weight: normal;
  padding: 1px 0;
}
#nav a, #nav a:hover {
  display: block;
  text-decoration: none;
  border-bottom: 1px solid #a5adbc;
  background: transparent url(../images/menu-arrow.gif) no-repeat 98% 50%;
  padding-right: 15px;
}
#nav span {
  display: block;
  cursor: pointer;
  white-space: nowrap;
}
#nav li ul span {
  white-space: normal;
}
#nav li.active a {
  color: #7d90ae;
}
#nav a {
  padding: 8px 12px 9px 8px;
  color: #797c7f;
}
#nav .level0 li a {
  padding-left: 10px;
}
#nav .level1 li a {
  padding-left: 20px;
}
#nav .level3 li a {
  padding-left: 30px;
}
#nav .level4 li a {
  padding-left: 40px;
}
#nav .level5 li a {
  padding-left: 50px;
}

/* Footer */
.informational {
  margin-top: 7px;
}
.informational ul {
  margin: 5px 10px 0 0;
  font-size: 11px;
  font-weight: normal;
}
.informational ul li {
  float: right;
  padding: 0 0 5px 7px;
}
.informational div {
  clear: both;
}
.legality {
  margin: 6px 0;
  text-align: center;
  color: #818181;
}
.footer-text {
  margin: 10px 15px !important;
}
.footer-text h5 {
  font-size: 110%;
}
.footer-text .ui-collapsible-content {
  font-size: 100%;
}
.footer {
  text-align: center;
  padding: 12px 16px;
  font-weight: normal;
  background: #3a3a3a;
  text-shadow: none;
}
.footer #copyright {
  margin-top: 8px;
  font-size: 12px;
  color: #717171;
}
.footer ul li {
  display: inline-block;
  margin: 0 4px;
}
.footer ul li a {
  font-size: 14px;
  color: #eee;
}
.hidden {
  display: none;
}
.footer-text .ui-btn-inner {
  border-top: 0;
}

/* Search form */
.search-form {
  width: 100%;
  height: 35px !important;
  border: 0;
  text-align: center;
}
.search-form-cont {
  background: #d1d1d1 url(../images/search-bg.gif) repeat-x bottom left;
  padding: 0 10px;
}
.search-form .col-1 {
  width: 75%;
}
.search-form .col-2 {
  width: 22%;
  padding: 8px 0 0 0;
}
.search-st {
  background: transparent url(../images/search-st.gif) no-repeat top left;
}
.search-end {
  background: transparent url(../images/search-end.gif) no-repeat top right;
}
.search-md {
  background: transparent url(../images/search-md.gif) repeat-x top right;
  height: 35px;
}
.search-md input {
  font-size: 12px;
  margin: 10px 0 0 10px;
  width: 190px;
  border: 0;
  background: transparent;
  color: #999;
}
.search-close, .search-go {
  background: transparent url(../images/search-close-but.gif) no-repeat top left;
  width: 20px;
  height: 20px;
  display: block;
  float: right;
}
.search-go {
  float: left;
  background-image: none;
}

/*Toolbar*/
.toolbar-wrapper.bottom {
  border-top: 1px solid #ddd;
}
.toolbar .pager {
  margin: 0 10px;
}
.toolbar .pager .limiter,
.toolbar .sorter-amount {
  display: none;
}
.toolbar .pager .pages {
  text-align: center;
}
.toolbar .ui-controlgroup-controls .ui-btn {
  border-style: solid;
  border-width: 1px 0 1px 1px;
  border-color: <?=$primary_color?>;
  color: <?=$primary_color?>;
  width: 20px;
}
.toolbar .ui-controlgroup-controls .ui-btn.current {
  color: #fff;
  background-color: #2c2c2c;
  opacity: 1;
}
.toolbar .ui-controlgroup-controls .ui-btn:active {
  color: #fff;
}
.toolbar .ui-controlgroup-controls .ui-btn.ui-last-child {
  border-right-width: 1px;
}

/* Contacts */
/*#contactForm {
  margin-bottom: 10px;
}
#contactForm h2 {
  font-size: 13px;
}*/

/* back links */
p.back-link a {
  display: none;
}

/* Store Switcher */
.store-switcher {
  text-align: center;
  padding: 8px 0 0;
  overflow: hidden;
  width: auto;
}
.store-switcher label {
  float: left !important;
  margin: 5px 0 0 -100px !important;
  left: 50% !important;
  font-size: 13px !important;
  text-align: right;
  width: 80px;
}
.store-switcher .ui-select {
  margin-right: -100px !important;
}
.store-switcher .ui-select .ui-btn {
  width: 120px;
}
.store-switcher .ui-select .ui-btn-icon-right .ui-btn-inner {
  font-size: 13px !important;
  padding: .4em 40px .4em 10px !important;
}
.store-switcher .ui-select .ui-btn-icon-right .ui-icon {
  right: 12px;
}

/* Currency */
.block-currency {
  margin: 10px;
}

/* Breadcrumbs */
.breadcrumbs {
  margin: 5px 5px 0 5px;
}
.crumbcontainer {
  white-space: nowrap;
  width: 95%;
  overflow: hidden;
  text-overflow: ellipsis;
}
.breadcrumbs {
  padding: 7px 5px;
  margin-top: 0;
}
.breadcrumbs .product {
  display: none;
}
.breadcrumbs ul li {
  display: inline;
  line-height: 1.35;
  font-size: 12px;
}
.breadcrumbs ul li span {
  padding: 0 3px;
}

/* Caregory */
.category-image {
  margin: 0;
  display: none;
}
.category-image img {
  width: 100%;
}
.category-products {
  margin: 0 5px;
}
.category-products .toolbar {
  clear: both;
  margin: 10px 0 15px 0;
}
.category-products .toolbar .sorter {
  font-size: 12px;
}
.category-products .toolbar .sorter label, .category-products .toolbar .sorter a, .category-products .toolbar .sorter span {
  margin-right: 4px;
}
.toolbar .sorter .label {
  display: inline-block;
  cursor: default;
}
.toolbar-bottom .pager ol {
  margin-top: 0 !important;
}
.products-grid .item {
  margin-bottom: 15px;
}
.products-grid .item .product-image {
  float: left;
  margin-right: 10px;
}
.products-grid .item .product-name {
  font-size: 14px;
}
/*.price {
  color: #76bd1d;
}*/
.products-grid .item .price-box {
  float: left;
  max-width: 160px;
  font-size: 12px;
}
.products-grid .item .price-box .regular-price {
  display: block;
  font-size: 15px;
}
.products-grid .item .ratings {
  float: left;
  width: 160px;
  margin: 5px 0;
}
.products-grid .item .actions {
  float: left;
}
.products-grid .item .actions button, .products-grid .item .actions .out-of-stock {
  margin: 8px 0;
  font-size: 15px;
}
.products-grid .item .actions .out-of-stock {
  color: #C00;
}
.products-grid .item .actions .add-to-links li {
  display: inline;
}
.catalog-category-view > a.ui-link > img {
  display: none;
}
.category-products {
  margin: 0;
}
.category-description {
  padding: 0 10px 10px;
  font-size: 110%;
}
.category-products .ui-li-heading {
  margin: .6em 0 .2em;
}
.category-products .price-box .price {
  font-weight: normal;
}
.category-products p.rating-links {
  display: none;
}
.category-products div.rating-box {
  position: absolute;
  left: 8px;
  z-index: 99;
  top: 65px;
}
.category-products div.price-box {
  line-height: 0.9em;
  margin: 8px 0 -8px 0;
}
.category-products ul.ui-listview .ui-btn-text > div.prices {
  position: absolute;
  top: 34px;
  left: 100px;
  font-size: 12px;
}
.category-products ul.ui-listview .ui-btn-text > div.prices .price-box a.product-link {
  display: block;
  line-height: 16px;
}
.category-products ul.ui-listview .ui-btn-text > div.prices a {
  color: inherit;
}
.category-products .prices .ui-li-desc {
  font-size: 12px !important;
  margin: -4px 0 5px !important;
}

/* Rating */
.rating-box {
  width: 65px;
  height: 12px;
  font-size: 0;
  line-height: 0;
  background: url(../images/bkg_rating.gif) 0 100% repeat-x;
  overflow: hidden;
}
.rating-box .rating {
  float: left;
  height: 13px;
  background: url(../images/bkg_rating.gif) 0 0 repeat-x;
}
.ratings .rating-box {
  float: left;
  margin-right: 5px;
}
.ratings .amount {
  font-size: 12px;
}
.rating-links {
  margin: 0;
}
.ratings .rating-links a {
  text-decoration: none;
}
.ratings .rating-links a:hover {
  text-decoration: none;
}
.ratings .rating-links .separator {
  margin: 0 3px;
}


/* ==========================================================================
  PRODUCT PAGE
   ========================================================================== */
.product-view {
  margin: 0 5px;
}
.product .product-name {
  color: #2c2c2c;
  font-size: 18px;
}
.product .product-subname {
  color: #8d8d8d;
  font-size: 15px;
}
.product-shop .email-friend, .product-shop .paypal-logo, .product-shop .add-to-box .or, .product-shop .add-to-box .add-to-links, .product-shop .add-to-links li:last-child {
  display: none;
}
.product-shop .add-to-links {
  font-size: 12px;
}
.product-shop .ratings {
  font-size: 12px;
}
.product-shop .availability-only, .product-shop .tier-prices {
  display: inline;
  margin: 0 5px 0 0;
  font-size: 12px;
}
.product-shop .availability {
  display: none;
}
.product-shop .short-description {
    padding: 12px;
    font-size: 14px;
    color: #8d8d8d;
}
.product-essential .product-img-box .zoom-notice, .product-essential .product-img-box .zoom, .product-essential .product-img-box .more-views {
  display: none;
}
.product-essential .add-to-cart .paypal-or {
  clear: both;
  display: block;
  margin: 3px 0 3px 60px;
}
.product-collateral {
  margin: 10px 0;
}
.product-collateral h2 {
  font-size: 14px;
}
.product-collateral .box-collateral {
  margin-bottom: 15px;
}
.product-collateral .products-grid td {
  vertical-align: top;
}
.product-collateral .products-grid .rating-links {
  display: none;
}
.product-collateral .products-grid .price-box {
  font-size: 12px;
}
.product-collateral .products-grid .ratings {
  margin-top: 5px;
}
.product-collateral .box-tags {
  display: none;
}
.product-collateral .up-sell .product-image img {
  border: 1px solid #ddd;
}
.product-options {
  margin: 22px 0 -9px;
}
.product-options .required em, .product-options p.required {
  display: none;
}
.product-options dt {
  padding: 0;
  margin: 0 0 5px 0;
}
.product-options dt label {
  font-size: 16px;
}
.product-options dd {
  margin: 0 0 10px;
}
.product-options dd img {
  padding: 4px 0 0;
  vertical-align: middle;
}
.product-options dd select {
  width: 100%;
}
.product-options dd .time-picker {
  display: inline-block;
  padding: 4px 0 0;
  vertical-align: middle;
}
.product-options dl.last dd.last {
  padding: 0;
  border-bottom: 0;
}
.product-options-bottom {
  margin: 0 0 10px;
}
.product-options-bottom .price-box {
  display: none;
}
.product-data {
  margin: 5px 0;
}
.product-data tbody td {
  padding: 3px;
}
.product-pricing, .product-attributes {
  margin: 5px 0;
}
.product-pricing {
  clear: both;
}
.product-attributes td {
  padding-right: 5px;
}
.product-attributes select {
  width: 100%;
}
.product-image img {
  border: 1px solid #ccc;
}
.product-price, .product-bundle-price {
  margin: 5px 0;
  font: bold 12px/14px Arial;
}
.price-box .price {
  white-space: nowrap;
  font-size: 18px;
  font-weight: normal;
}
/*.old-price {
  float: right;
}*/
.old-price .price-label {
  display: none;
}
.old-price .price {
  font-size: 14px;
  text-decoration: line-through;
  margin: 0;
}
.minimal-price-link {
  display: block;
}
.minimal-price-link .label,
.minimal-price-link .price {
  font-size: 12px;
}
.configured-price .price-label {
  font-weight: bold;
  white-space: nowrap;
  display: none;
}
.configured-price .price {
  font-weight: 500;
}
.catalog-product-view div.product-name {
  margin: 0 -10px 0;
  padding: 10px;
}
.catalog-product-view div.product-name h1 {
  text-align: center;
  font-weight: 300;
  font-size: 28px;
  line-height: 1.2;
}


.catalog-product-view .footer.cart-active {
  padding-bottom: 20px;
  position: relative;
  bottom: 3rem;
}

.checkout-cart-index .footer.cart-active{
  display: none;
}

.checkout-cart-index .footer,
.catalog-product-view .footer {
  padding-bottom: 70px;
}

.ui-listview > li .price-box p {
  margin: 0;
}
.ui-listview > li .product-name {
  margin-bottom: 0;
}
#quick-info img, #quick-info object, #quick-info iframe, #product-tabs img, #product-tabs object, #product-tabs iframe {
  max-width: 100%;
  height: auto;
}
.catalog-product-view #mobileproductview .breadcrumbs {
  float: left;
  margin-left: -5px;
  margin-bottom: 10px;
}
.catalog-product-view strong.crumbname {
  line-height: 30px;
  margin-left: -7px;
  font-size: 12px;
}
.catalog-product-view #messages_product_view {
  clear: both;
}
.product-name h1.ui-title {
  margin: .6em 10px .8em !important;
}
#va-addtocart {
  background: <?=$primary_color?>;
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  z-index: 25;
  font-size: 18px;
  -webkit-transform-style: preserve-3d;
}
#va-addtocart .va-add-to-cart {
  background: <?=$secondary_color?>;
  text-align: center;
  padding: 14px 0;
  color: #fff;
  width: 66%;
  float: right;
}
#va-addtocart .va-add-to-cart:active {
  background: <?=$primary_color?>;
}
#va-addtocart .va-add-to-cart i {
  font-style: normal;
  vertical-align: super;
  font-size: 10px;
}
#va-addtocart #va-qty {
  float: left;
  width: 34%;
  padding: 14px 0;
  position: relative;
  color: #fff;
  text-align: center;
  background-color: <?=$primary_light_color?>;
}
#va-addtocart #va-qty:active {
  background-color: <?=$primary_color?>;
}
#va-addtocart #va-qty.ds-product-qty:active {
    background-color: <?=$primary_light_color?>;
}
#va-addtocart .va-qty-drop-down {
  border-radius: 2px;
  border: 2px solid rgba(0,0,0,0.08);
  background: #fff;
  bottom: 51px;
  left: 50%;
  margin-left: -45px;
  width: 90px;
  opacity: 0;
  visibility: hidden;
  position: absolute;
  z-index: 50;
  -webkit-transform: translateY(-10px);
          transform: translateY(-10px);
}
#va-addtocart .va-qty-drop-down,
#va-addtocart #va-qty i.fa {
  -webkit-transition: all .25s;
          transition: all .25s;
}
#va-addtocart #va-qty.va-open .va-qty-drop-down {
  opacity: 1;
  visibility: visible;
  -webkit-transform: translateY(0);
          transform: translateY(0);
}
#va-addtocart #va-qty.va-open i.fa {
  -webkit-transform: rotate(180deg);
          transform: rotate(180deg);
}
#va-addtocart .va-qty-drop-down::after,
#va-addtocart .va-qty-drop-down::before {
  content: '';
  position: absolute;
  left: 50%;
  margin-left: -8px;
  bottom: -8px;
  border-right: 8px solid transparent;
  border-left: 8px solid transparent;
}
#va-addtocart .va-qty-drop-down::after {
  border-top: 8px solid #fff;
}
#va-addtocart .va-qty-drop-down::before {
  border-top: 8px solid rgba(0,0,0,0.08);
  bottom: -10px;
}
#va-addtocart .va-qty-drop-down li {
  text-align: center;
  position: relative;
  height: 44px;
  line-height: 44px;
  color: #8d8d8d;
  border-bottom: 1px solid #e8e8e8;
  -webkit-transition: all .25s;
          transition: all .25s;

}
#va-addtocart select {
  display: none;
}
#va-addtocart .va-qty-drop-down li:last-child {
  border-bottom: 0 none;
}
#va-addtocart .va-qty-drop-down li:active {
  background-color: #c2c2c2;
  color: #fff;
  -webkit-transition: none;
          transition: none;
}
#va-addtocart .va-qty-drop-down li.active {
  background-color: <?=$primary_color?>;
  color: #fff;
}
#va-addtocart .va-qty-drop-down li.va-more.active,
#va-addtocart .va-qty-drop-down li.va-more:active {
  background-color: transparent;
}
#va-addtocart .va-qty-drop-down li.va-more input {
  width: 100%;
  position: absolute;
  top: 0;
  left: 0;
  border: 0 none;
  text-align: center;
  border-radius: 0 0 2px 2px;
  color: #8d8d8d;
  height: 44px;
  line-height: 44px;
  -webkit-box-sizing: border-box;
     -moz-box-sizing: border-box;
          box-sizing: border-box;
}
#va-addtocart .va-qty-drop-down li.va-more.active input {
  color: #fff;
  background-color: <?=$primary_color?>;
}

/* tags */
.product-tags {
  margin-bottom: 10px;
}
.product-tags li {
  display: inline;
  padding: 5px;
}

/* video */
.video_container a {
  width: 100%;
}
.video_container img {
  width: 100% !important;
  height: auto !important;
}

/* Tabs */
#additional-info > div {
  display: none;
}
#additional-info > div.box-additional {
  display: block;
}
.product-view {
  margin: 0;
}
h2.details, .box-tags h2 {
  padding: 10px 10px;
  margin: 10px -10px;
  font-size: 18px;
  font-weight: normal;
}
#quick-info h2 {
  font-size: 110%;
}
#product-tabs .form-add h2, #product-tabs .form-add label, #addTagForm label {
  font-size: 110%;
}
#product-tabs div.product-tab ul {
  list-style: square;
  margin: 10px 0 10px 18px;
}
#product-tabs div.product-tab ul.form-list {
  list-style: none;
  margin: 0;
}
#product-tabs div.product-tab .box-reviews h2 {
  font-size: 130%;
}
.special-price .price-label {
  display: none !important;
  float: left;
}
.product-shop .price-box .price-label {
  font-size: 15px;
  font-weight: bold;
  display: block;
}
.product-shop .price-box .old-price {
  text-decoration: line-through;
  margin-top: 12px;
}
.product-shop .add-to-box {
  clear: both;
}
.product-shop .availability, .product-shop .availability-only, .product-shop .tier-prices {
  display: none;
}
.product-options-bottom .add-to-links {
  display: none;
}
.product-view .data-table th, .product-view .data-table td {
  background: none;
  border: 0;
  text-align: left;
}
.product-view #product-tabs {
  margin: 10px 0 !important;
}
.single-product-page .price-box {
  margin: -5px 0 0.15em;
}
.single-product-page .price-box .price {
  font-size: 25px;
}
.single-product-page .special-price {
  color: <?=$secondary_color?>;
}
.single-product-page .special-price .price:before {
    content: 'SALE ';
}
.single-product-page .price-box .special-price .price {
  font-size: 23px;
}
.single-product-page .old-price {
  float: none;
}
.single-product-page .strikePrice {
  text-decoration: line-through;
}
.single-product-page .salePrice {
  color: <?=$secondary_color?>;
  margin-left: 6px;
}
.single-product-page .price-box .old-price .price {
  font-size: 18px;
  line-height: 2.3;
}
.single-product-page .price-label, .single-product-page .add-to-cart .ui-field-contain {
  display: none !important;
}
.va-product-actions {
  margin-top: 22px;
}
.va-product-actions .va-social {
  text-align: justify;
  font-size: 0;
}
.va-product-actions .va-social:after {
  content: "";
  width: 100%;
  display: inline-block;
}
.va-product-actions .va-social .button {
  margin-bottom: 22px;
  width: 30.5%;
  display: inline-block;
}
.catalog-product-view #quick-info h2 {
  display: none !important;
}
.catalog-product-view  h2.details {
  padding: 0;
  margin: 0;
  font-size: 15px;
}
.catalog-product-view #quick-info p {
  color: #8d8d8d;
  text-align: justify;
}
.catalog-product-view .product-tab {
  border-top: 1px solid #e8e8e8;
  margin-top: 0;
}
.catalog-product-view .product-tab:last-child {
  border-bottom: 1px solid #e8e8e8;
}
.catalog-product-view .product-tab h2 a {
  color: #8d8d8d !important;
  font-size: 15px;
}
.catalog-product-view .product-tab h2 a:active {
  background: #e8e8e8;
}
.catalog-product-view .product-tab h2 a:hover,
.catalog-product-view .product-tab h2 a:active {
  border-color: #ddd;
}
.catalog-product-view .ui-btn:after {
  opacity: 0.4;
}
.catalog-product-view  .ui-btn {
  padding-top: 14.5px;
  padding-bottom: 14.5px;
}
.catalog-product-view  .ui-collapsible-set, .catalog-product-view .product-view #product-tabs {
  margin-bottom: 0 !important;
}
.catalog-product-view .upsell-products {
  margin-bottom: 60px;
}
.upsell-products ul.ui-listview .ui-btn-text > div.prices {
  position: absolute;
  top: 42px;
  left: 100px;
  font-size: 12px;
}
.upsell-products ul.ui-listview .ui-btn-text > div.prices .price-box a.product-link {
  display: block;
  line-height: 16px;
}
.upsell-products ul.ui-listview .ui-btn-text > div.prices a {
  color: inherit;
}

/* As seen in */
#mobileproductview .as-seen-in li {
  border-top: 1px solid #ececec;
  text-align: center;
  padding: 7px;
  -webkit-box-sizing: border-box;
     -moz-box-sizing: border-box;
          box-sizing: border-box;
}
#mobileproductview .as-seen-in li:active {
  background: #c2c2c2;
}
#mobileproductview .as-seen-in li img {
  max-height: 90px;
}
#mobileproductview .as-seen-in li.as-halfs {
  width: 50%;
  float: left;
  height: 65px;
}
#mobileproductview .as-seen-in li.as-halfs img {
  max-height: 50px;
  max-width: 140px;
  vertical-align: middle;
}
#mobileproductview .as-seen-in li.as-halfs a::after {
  content: '';
  display: inline-block;
  vertical-align: middle;
  height: 100%;
}

/* Product review */
.review-product-list .product-view {
  font-size: 12px;
}
.box-reviews .form-add {
  margin-top: 15px;
}
#reviews .form-add label {
  color: #fff;
}
.box-reviews dd {
  margin-bottom: 10px;
}
#review-form h3, #review-form label em, .review-product-list .product-essential .nobr, .box-reviews .pager {
  display: none;
}

/* Cart */
.cart {
  margin: 0 5px;
}
.cart .cart-collaterals .col-2 {
  width: 100%;
}
.cart .shipping {
  font-size: 12px;
}
.cart .shipping h2 {
  font-size: 14px;
}
.cart .shipping select {
  width: 100%;
}
.cart .shipping .buttons-set {
  margin-top: 5px;
  text-align: right;
}
.cart .totals {
  float: right;
  /*margin-bottom: 15px;*/
  font-size: 12px;
}
.cart .totals strong {
  font-size: 14px;
}
.cart .cart-table {
  border-collapse: collapse;
  margin: 0;
  width: 100%;
}
.cart .cart-table th, .cart .cart-table td {
  padding: 4px;
}
.cart .cart-table td {
  color: #8d8d8d;
}
.cart .cart-table td .item-options {
  font-size: 12px;
  color: #999;
}
.cart .cart-table td input {
  text-align: center;
  border: 0 none;
  height: auto;
  line-height: 51px;
  height: 51px;
  width: 40px;
  font-size: 16px;
  border: 1px dashed #ccc;
}
.cart .cart-table h2 {
  margin: 0;
  font-weight: normal;
  font-size: 14px;
}
.cart .cart-table h2 a {
  color: #8d8d8d;
}
.cart .checkout-types li {
  clear: both;
  text-align: center;
}
.cart .checkout-types .paypal-or {
  clear: both;
  display: block;
}
.cart .checkout-types .btn-checkout span {
  display: none;
}
.cart .shopping-continue {
  float: left;
  padding-top: 4px;
  font-size: 12px;
}
.cart {
  margin: 0;
}
#shopping-cart-table {
  padding: 0 !important;
  margin: 0 0 24px;
  border-top: 1px solid #e8e8e8;
}
#shopping-cart-table .ui-icon-delete {
  margin: 0;
}
#shopping-cart-table th:nth-child(2),
#shopping-cart-table td:nth-child(2) {
  width: 75px;
}
#shopping-cart-table th:nth-child(4),
#shopping-cart-table td:nth-child(4),
.shopping-continue a {
  display: none;
}
#shopping-cart-table th:nth-child(7),
#shopping-cart-table td:nth-child(7) {
  display: none;
}
.cart .totals {
  padding: 10px;
}
.cart .checkout-types .btn-checkout {
  background-image: none;
  width: 100%;
}
.discount, .cart .cart-table th {
  background: transparent;
}
div.cart-empty {
  padding: 10px;
}
.discount {
  clear: both;
  /*display: block;*/
  margin-bottom: 15px;
  padding: 5px 10px;
  font-size: 12px;
}
.discount h2 {
  font-size: 18px;
}

/* Checkout */
.opc {
  /*margin: 0 5px 15px 5px;*/
}
.opc-progress-container {
  display: none;
}
.opc .fields label, .opc .wide label {
  display: block;
  margin-bottom: 3px;
  font-size: 12px;
}
/*.opc li li li {
  margin-bottom: 10px;
}
.opc .input-box {
  margin-bottom: 5px;
}*/
.opc .ui-collapsible-set {
  margin: 0;
}
.opc .step-title .number {
  font-weight: bold;
}
.opc .step-title .number:after {
  content: ")";
}
ol.ui-listview.opc > li.ui-li-static:before {
  font-size: 20px;
  margin-top: 6px;
  display: none;
}
ol.ui-listview.opc > li * {
  text-indent: 0 !important;
}
.ui-listview.opc > .ui-li-static {
  white-space: normal;
}
.opc .ui-link {
  font-size: 14px;
}
.opc .required {
  display: block;
}
.opc .step-title h2 {
  display: inline-block;
  margin: 0;
  height: 28px;
  line-height: 28px;
  padding-top: 3px;
  font-size: 20px;
  font-weight: normal;
}
.opc .ui-collapsible-heading {
  width: 100%;
  border: 1px solid #eee;
}
.opc .ui-collapsible-heading .ui-btn {
  font-size: 14px;
  font-weight: normal;
  padding-left: 38px;
}
.opc .step-title a {
  display: none;
}
.opc .step h3 {
  font-size: 12px;
}
.opc .form-list li {
  padding: 0;
}
.opc .form-list .control {
  margin-top: 10px;
}
.opc .buttons-set {
  text-align: right;
}
.opc .back-link {
  float: left;
}
.opc .sp-methods {
  font-size: 12px;
}
.opc .please-wait {
  float: left;
}
#checkout-review-table h3 {
  white-space: normal;
}
#checkoutSteps label {
  font-weight: normal !important;
}
.opc .back-link {
  font-size: 12px;
}
.opc label[for=p_method_paypal_express] a {
  display: none;
}
#opc-payment dl {
  margin: 0 !important;
  padding-top: 10px;
}
#opc-payment dd {
  margin-bottom: 10px;
}
#payment_form_ccsave label {
  clear: both;
  display: block;
  margin: 10px 0 5px 0;
}
#payment_form_ccsave .month {
  width: 110px;
}
#payment_form_ccsave .year {
  width: 60px;
}
.payment-methods dt, .payment-methods dd {
  padding: 3px 0;
}
.payment-methods .centinel-logos a {
  margin-right: 3px;
}
.payment-methods .centinel-logos img {
  vertical-align: middle;
}
.checkout-review tfoot td {
  padding: 3px;
}
.p-methods {
  text-align: center;
  padding: 10px;
}
.centinel .authentication {
  border: 1px solid #ddd;
  background: #fff;
}
.centinel .authentication iframe {
  width: 99%;
  height: 400px;
  background: transparent !important;
  margin: 0 !important;
  padding: 0 !important;
  border: 0 !important;
}
.col2-set .col-1, .col2-set .col-2 {
  width: 100%;
}
#checkout-step-login {
  position: relative;
}
#checkoutSteps fieldset {
  width: 100%;
}
#checkoutSteps .number {
  display: none;
}
#checkoutSteps .cvv-what-is-this {
  margin-top: 14px;
  display: inline-block;
}
#checkoutSteps .step-title h2 {
  margin: 0;
}
#checkoutSteps .step-title a {
  float: right;
  margin-top: -20px;
}
#checkout-step-login .col2-set .col-1 h3, #checkout-step-login .col2-set .col-2 h3 {
  margin-top: 0;
  margin-bottom: 0;
}
#checkout-step-login .ui-collapsible-content {
  margin: 0 !important;
}
ul.ul {
  list-style-type: disc;
  margin-left: 20px;
  font-size: 13px;
}
#checkoutSteps label.ui-input-text, #checkoutSteps label.ui-select {
  font-size: 14px;
}
#checkout-step-review {
  margin-top: 10px;
}
.inchoo-socialconnect-checkout {
  padding: 5px 12px;
}
.inchoo-socialconnect-checkout .facebook-btn {
  margin: 14px 0 0;
}

/* Checkout Success */
.checkout-success {
  padding: 12px;
}

/* Wishlist */
ul#wishlist {
  margin: 0 -10px 18px;
}
#wishlist-table {
  border-collapse: collapse;
}
#wishlist-table th {
  background: #CCC;
}
#wishlist-table td, #wishlist-table th {
  padding: 4px;
}
#wishlist-table h2 {
  font-size: 12px;
}
#wishlist-table .regular-price {
  font-size: 14px;
}
#wishlist-table textarea {
  clear: both;
  width: 100%;
  margin: 3px 0 11px 0;
}
#wishlist-table .button {
  clear: both;
  display: block;
  margin-bottom: 10px;
  font-size: 12px;
}
#wishlist-table .btn-remove2 {
  color: #C00;
}
#wishlist-table .odd td {
  padding-top: 15px;
}
#wishlist-table .even td {
  border-bottom: 1px solid #CCC;
}
.my-wishlist .buttons-set {
  margin-top: 15px;
}
.my-wishlist .buttons-set .btn-share {
  display: none;
}
.my-wishlist .buttons-set .btn-update {
  float: right;
}
.my-wishlist .buttons-set button {
  display: inline-block;
  margin-right: 5px;
  font-size: 14px;
}

/* My account */
.my-account {
  padding: 0 10px;
}
.my-account .page-title, .customer-account-logoutsuccess .page-title {
  margin: 0 -10px;
}
.my-account .welcome-msg, .my-account .dashboard .box-info .box-head, .my-account .pager, .my-account label em, .my-account .buttons-set .required {
  display: none;
}
.my-account h2 {
  font-size: 14px;
}
.my-account .box-title h3, .my-account .box-head h2 {
  display: inline;
}
.my-account .box-title a, .my-account .box-head a {
  float: right;
}
.my-account .col2-set .col-1, .my-account .col2-set .col-2 {
  clear: both;
  width: 100%;
  margin-bottom: 15px;
}
.my-account .buttons-set {
  text-align: right;
}
.my-account .buttons-set .back-link {
  float: left;
}
#my-orders-table {
  margin: 5px 0 10px 0;
}
#my-orders-table th {
  text-align: left;
}
.account-login .new-users {
  text-align: left;
}
.account-login .registered-users .ui-btn-block {
  display: inline-block;
}
.account-login .page-title {
  margin: 0 -14px;
}
.forgot-password .buttons-set a {
  display: none;
}
.box-account.box-recent,
.dashboard .box {
  padding: 10px;
}

/* Login, retrieve password, registration */
.account-login {
  padding: 0 14px;
}
.account-login .facebook-btn {
  margin: 0 0 22px;
}
.account-login .registered-users {
  margin-bottom: 10px;
}
.account-login .registered-users .validation-advice {
  clear: both;
  padding-top: 3px;
  text-align: right;
}
.account-login .registered-users .buttons-set {
  text-align: right;
}
.account-login .new-users {
  text-align: right;
}
.account-login .new-users .content {
  line-height: 14px;
}
.account-login .new-users .button-set {
  text-align: right;
}
.forgot-password {
  padding: 0 10px 5px;
  font-size: 12px;
}
.forgot-password .validation-advice {
  clear: both;
  padding-top: 3px;
  text-align: right;
}
.forgot-password .buttons-set {
  clear: both;
  padding-top: 10px;
  text-align: right;
}
.forgot-password .buttons-set a {
  float: left;
}
.account-create .facebook-btn {
  margin: 0 0 22px;
}
.account-create form {
  display: block;
  padding: 0 10px 15px;
}
.account-create h2 {
  font-size: 14px;
}
.account-create em {
  display: none;
}
.account-create .field {
  clear: both;
}
.account-create .checkbox {
  margin-left: 0px;
}
.account-create .buttons-set {
  text-align: right;
}
.account-create .buttons-set .back-link {
  float: left;
}
.account-create .buttons-set .required {
  display: none;
}
.account-create .fieldset {
  padding-top: 5px;
}

/* Other tech styles */
.button-set {
  margin-top: 10px;
}
.button-set .form-buttons {
  text-align: right;
}
.data-table {
  font-size: 14px;
}
.data-table tr {
  border-bottom: 1px solid #e8e8e8;
}
.data-table th {
  text-align: left;
  color: #888;
}
.data-table td {
  padding: 2px 3px;
  vertical-align: middle;
}
.data-table td.label {
  background: #E4E4E4;
  text-align: right;
  border-top: 1px solid #fff;
}
.data-table td.data {
  background: #ECECEC;
  border-top: 1px solid #fff;
  border-left: 1px solid #fff;
}
.data-table td .ui-input-text {
  margin: 0;
}
.data-table td .price {
  color: #8d8d8d;
  font-size: 16px
}
.link-remove {
  color: #df280a;
}
.link-cart {
  color: #3d6611;
}
.form-list li .col-1 {
  text-align: right;
  padding-top: 3px;
}
.v-fix {
  float: left;
}
.v-fix select {
  width: 95px;
}

/* Messages =============================================================================== */
label em {
  display: none;
}
label.required:after {
  content: '*';
}
p.required {
  text-align: right;
}
.success {
  color: #3d6611;
}
input.validation-failed {
  border-bottom: 1px solid #e00b1d !important;
}
.error, .validation-advice {
  color: #df280a;
  padding: 3px 0 7px;
  float: left;
  font-size: 12px;
}
.validation-advice {
  display: none;
}
input[type=radio] + .validation-advice,
input[type=checkbox] + .validation-advice,
select + .validation-advice {
  display: block;
  padding: 3px 0 0;
  font-size: 12px;
  float: none;
  text-transform: none;
  font-weight: normal;
}
.notice {
  color: #e26703;
}
.success, .error {
  font-weight: bold;
}
.messages, .messages ul {
  list-style: none !important;
  margin: 0 !important;
  padding: 0 !important;
}
.messages {
  width: 100%;
  overflow: hidden;
}
.error-msg, .success-msg, .notice-msg, .note-msg {
  margin: 10px !important;
  padding: 8px !important;
  border-style: solid !important;
  border-width: 1px !important;
  background-repeat: no-repeat !important;
  background-position: 10px 10px !important;
  text-align: center !important;
  font-size: .95em !important;
  border-radius: 0 !important;
}
.error-msg li, .success-msg li, .notice-msg li {
  margin-bottom: .2em;
}
.error-msg {
  border-color: #f16048;
  background-color: #faebe7;
  color: #df280a;
}
.success-msg {
  border-color: #69A661;
  background-color: #fff;
  color: #69A661;
}
.notice-msg, .note-msg {
  border-color: #fcd344;
  background-color: #fafaec;
  color: #997707;
}

/* For Demo store only */
.demo-notice {
  margin: 0;
  padding: 5px 10px 6px 10px;
  background: #d75f07;
  text-align: center;
  line-height: 1em;
  color: #FFF;
}

/* ======================================================================================== */
/* Noscript notice */
.noscript {
  background: #ffff90;
  font-size: 12px;
  line-height: 1.25;
  color: #2f2f2f;
}
.noscript .noscript-inner {
  margin: 0 auto;
  padding: 12px 10px 12px 70px;
  background: url(../images/i_notice.gif) 20px 50% no-repeat;
}
.noscript p {
  margin: 0;
}

/* ======================================================================================== */
/* Clearfixes ================================================================================= */
.page-head:after, .page-head-alt:after, .clear:after, .col2-set:after, .col3-set:after, .col4-set:after, .col2-alt-set:after, .head:after, .inner-head:after, .header-top:after, .quick-access:after, .header-nav:after, #nav:after, .middle:after, .product-essential:after, .button-set:after, .actions:after, .legend:after, .form-list li:after, .button-container:after, .ratings:after, .page-head:after, .page-head-alt:after, .group-select li:after, .search-autocomplete li:after, .side-col li:after, .account-box li:after, .address-list li:after, .generic-product-list li:after, .listing-type-list .listing-item:after, .listing-type-list .product-info .product-reviews:after, .my-review-detail:after, .shopping-cart-totals .checkout-types:after, .products-grid .item:after, .discount .discount-form:after, .cart .totals:after, .products-grid .item:after {
  content: ".";
  display: block;
  clear: both;
  height: 0;
  font-size: 0;
  line-height: 0;
  visibility: hidden;
  overflow: hidden;
}

/* ======================================================================================= */
/* Remember Me and MAP popups */
.window-overlay {
  display: none;
}
.map-info a {
  font-size: 14px;
}
a.map-popup-close {
  font-size: 20px;
}
.catalog-category-view .map-info a {
  display: none;
}
.catalog-category-view .map-info a.product-link {
  display: block;
}
.map-popup, .popup-block, .remember-me-popup {
  z-index: 999999;
  background: #fcfcfc;
  position: absolute;
  left: 15px !important;
  right: 15px !important;
  padding: 15px;
  -webkit-box-shadow: 0 2px 6px rgba(0, 0, 0, .25);
  -moz-box-shadow: 0 2px 6px rgba(0, 0, 0, .25);
  box-shadow: 0 2px 6px rgba(0, 0, 0, .25);
}
#remember-me-popup .remember-me-popup-close {
  display: none;
}
.popup-block {
  background: #fff;
  display: none;
  position: fixed;
  top: 50%;
  left: 50%;
  margin: -60px 0 0 -140px;
  width: 250px;
}
.popup-block .form-list {
  margin: 0 0 15px;
}
.popup-block .block-title {
  display: none;
}
.popup-block .buttons-set button {
  display: inline-block !important;
  margin-right: 1px !important;
  width: 49% !important;
}
.popup-block.active {
  display: block;
}
.map-popup-close {
  float: right;
}
.map-popup-heading, .map-popup-checkout, .map-popup-price {
  margin: 0 0 10px;
}
.map-popup-price .special-price .price-label {
  color: #222;
  display: none;
}
.map-popup-price .special-price .price {
  font-weight: bold;
}
.remember-me-popup-body {
  line-height: 20px;
}
.remember-me-popup-close {
  background: #fff;
  border: 1px solid #e4e4e4;
  color: #424242;
  display: block;
  margin: 15px auto 0;
  text-align: center;
  font-weight: bold;
  font-variant: small-caps;
  text-transform: lowercase;
  text-shadow: 0 -1px 0 #eee;
  padding: 5px 10px;
  width: 100px;
  -webkit-box-shadow: 0 1px 2px rgba(0, 0, 0, .1);
  -moz-box-shadow: 0 1px 2px rgba(0, 0, 0, .1);
  box-shadow: 0 1px 2px rgba(0, 0, 0, .1);
}
#remember-me-box a {
  font-weight: bold;
  border-bottom: 1px dashed;
}

/* ************** Photoswipe ************** */
body.ps-active, body.ps-building, div.ps-active, div.ps-building {
  background: #000;
  overflow: hidden;
}
body.ps-active *, div.ps-active * {
  -webkit-tap-highlight-color: rgba(255, 255, 255, 0);
  display: none;
}
body.ps-active *:focus, div.ps-active *:focus {
  outline: 0;
}

/* Document overlay */
div.ps-document-overlay {
  background: #000;
}

/* UILayer */
div.ps-uilayer {
  background: #000;
  cursor: pointer;
}

/* Zoom/pan/rotate layer */
div.ps-zoom-pan-rotate {
  background: #000;
}
div.ps-zoom-pan-rotate * {
  display: block;
}

/* Carousel */
div.ps-carousel-item-loading {
  background: url(../images/loader.gif) no-repeat center center;
}
div.ps-carousel-item-error {
  background: url(../images/error.gif) no-repeat center center;
}

/* Caption */
div.ps-caption {
  background: #000000;
  background: -moz-linear-gradient(top, #303130 0%, #000101 100%);
  background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #303130), color-stop(100%, #000101));
  border-bottom: 1px solid #42403f;
  color: #ffffff;
  font-size: 13px;
  text-align: center;
}
div.ps-caption * {
  display: inline;
}
div.ps-caption-bottom {
  border-top: 1px solid #42403f;
  border-bottom: none;
  min-height: 44px;
}
div.ps-caption-content {
  padding: 13px;
  display: block;
}

/* Toolbar */
div.ps-toolbar {
  background: #000000;
  background: -moz-linear-gradient(top, #303130 0%, #000101 100%);
  background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #303130), color-stop(100%, #000101));
  border-top: 1px solid #42403f;
  color: #ffffff;
  font-size: 13px;
  text-align: center;
  height: 44px;
  display: table;
  table-layout: fixed;
}
div.ps-toolbar * {
  display: block;
}
div.ps-toolbar-top {
  border-bottom: 1px solid #42403f;
  border-top: none;
}
div.ps-toolbar-close, div.ps-toolbar-previous, div.ps-toolbar-next, div.ps-toolbar-play {
  cursor: pointer;
  display: table-cell;
}
div.ps-toolbar div div.ps-toolbar-content {
  width: 44px;
  height: 44px;
  margin: 0 auto 0;
  background-image: url(../images/icons.png);
  background-repeat: no-repeat;
}
div.ps-toolbar-close div.ps-toolbar-content {
  background-position: 0 0;
}
div.ps-toolbar-previous div.ps-toolbar-content {
  background-position: -44px 0;
}
div.ps-toolbar-previous-disabled div.ps-toolbar-content {
  background-position: -44px -44px;
}
div.ps-toolbar-next div.ps-toolbar-content {
  background-position: -132px 0;
}
div.ps-toolbar-next-disabled div.ps-toolbar-content {
  background-position: -132px -44px;
}
div.ps-toolbar-play div.ps-toolbar-content {
  background-position: -88px 0;
}

/* Hi-res display */
@media only screen and (-webkit-min-device-pixel-ratio:1.5), only screen and (min--moz-device-pixel-ratio:1.5), only screen and (min-resolution:240dpi) {
div.ps-toolbar div div.ps-toolbar-content {
  -moz-background-size: 176px 88px;
  -o-background-size: 176px 88px;
  -webkit-background-size: 176px 88px;
  background-size: 176px 88px;
  background-image: url(../images/icons@2x.png);
}
}
.product-img-box {
  clear: both;
  text-align: center;
  margin-bottom: 12px;
}
.gallery {
  list-style: none;
  padding: 0;
  margin: 0;
}
.gallery:after {
  clear: both;
  content: ".";
  display: block;
  height: 0;
  visibility: hidden;
}
.gallery li {
  float: left;
}
.gallery li a {
  display: block;
  margin: 10px 10px 5px 0;
}
.gallery li img {
  display: block;
  width: 100%;
  height: auto;
}

/* Drop Down Navigation */
.more_drop, .shop_drop {
  position: absolute;
  z-index: 100;
  left: 0;
  width: 100%;
  border-bottom: 1px solid #ddd;
}
.more_drop h2, .shop_drop h2 {
  margin: 0 !important;
}

/* 404 Page */
.cms-index-noroute {
  padding: 24px;
}
.cms-index-noroute .disc {
  margin-top: 12px;
  list-style: disc;
  padding-left: 24px;
}
.cms-index-noroute .disc .ui-link {
  color: #000;
}

/* Facebook button */
.facebook-btn {
  margin: 14px;
}
.facebook-btn.ui-btn, .facebook-btn #va-facebook {
  padding: 0 !important;
  margin: 0 !important;
}
.facebook-btn p {
  font-size: 15px;
  margin: 8px 0 0;
  text-transform: uppercase;
  font-weight: 500;
}
.facebook-btn h2 {
  font-size: 20px;
  margin: 0;1
  font-weight: normal;
  line-height: 1;
}
#va-facebook {
  position: relative;
  display: inline-block;
  width: 100%;
  border-color: transparent;
  color: #fff;
  text-shadow: none;
  background: <?=$primary_color?>;
}
#va-facebook i.fa-angle-right {
  position: absolute;
  right: 18px;
  font-size: 36px;
  top: 8px;
}
#va-facebook i.fa-facebook {
  text-align: center;
  font-size: 26px;
  float: left;
  width: 54px;
  height: 54px;
  line-height: 54px;
  margin-right: 12px;
  background: <?=$secondary_color?>;
}
#va-facebook:active {
  background-color: <?=$secondary_color?>;
}

/* Questionnair */
.mt-questionnaire-wrapper {
  background-color: #E6EDE8;
  overflow: hidden;
}
.mt-questionnaire-wrapper h1 {
  text-align: center;
  font-weight: 100;
  font-size: 22px;
  margin: 0;
  padding: 20px 10px;
  border-bottom: 1px solid #C9CCCA;
}
.mt-questionnaire-wrapper .mt-progress {
  text-align: center;
  position: relative;
  margin: 10px 0 15px;
}
.mt-questionnaire-wrapper .mt-progress .mt-label {
  color: #A65C8A;
  font-size: 110px;
  height: 184px;
  left: 0;
  line-height: 185px;
  margin: auto;
  position: absolute;
  right: 0;
  top: 8px;
  width: 184px;
  text-align: center;
}
.mt-questionnaire-wrapper .mt-questions ul li .ui-radio {
  display: none;
}
.mt-questionnaire-wrapper .mt-questions ul li .ui-link-inherit {
  padding: 15px 30px 15px 15px;
  font-size: 18px;
  font-weight: 100;
}
.mt-questionnaire-wrapper .mt-questions .mt-slide {
  display: none;
}
.mt-questionnaire-wrapper .mt-questions .mt-slide.active {
  display: block;
}
.mt-message {
  text-align: center;
  padding: 10px;
  font-size: 16px;
  line-height: 1.4;
  border-bottom: 1px solid #bbb;
}
.mt-social {
  text-align: center;
}
.mt-social h1 {
  font-weight: 100;
  font-size: 24px;
  margin: 20px 0;
}
.mt-product-tools {
  padding: 10px;
}
.mt-product-tools .social_container {
  padding: 0;
}

@media only screen and (min--moz-device-pixel-ratio: 2),
only screen and (-o-min-device-pixel-ratio: 2/1),
only screen and (-webkit-min-device-pixel-ratio: 2),
only screen and (min-device-pixel-ratio: 2) {
  .rating-box, .rating-box .rating {
    background-image: url(../images/bkg_rating@2.png);
    background-size: 13px auto;
  }
}

#shopping-cart-table .item-options dt {
  font-weight: bold;
}
#shopping-cart-table .cart-extra-desc h4,
#shopping-cart-table .cart-extra-desc p {
  font-size: 12px;
}

/*----------------------------------------------- Ellipsis on Category page ----------------------------------------*/
.ellip {
  display: block;
  height: 100%;
}

.ellip-line {
  display: inline-block;
  text-overflow: ellipsis;
  white-space: nowrap;
  word-wrap: normal;
  max-width: 100%;
}

.ellip,
.ellip-line {
  position: relative;
  overflow: hidden;
}

/*----------------------------------------------- Modal ------------------------------------------------------------*/

.nwg-overlay,
#nwg-modal-container,
.nwg-modal {
  -webkit-transition: all .25s;
     -moz-transition: all .25s;
      -ms-transition: all .25s;
       -o-transition: all .25s;
          transition: all .25s;
}
.nwg-overlay {
  position: fixed;
  top: 0;
  right: 0;
  left: 0;
  bottom: 0;
  background-color: #000;
  z-index: 1000;
  opacity: 0;
  filter: alpha(opacity=0);
  visibility: hidden;
}
.nwg-show-overlay {
  opacity: 0.4;
  filter: alpha(opacity=40);
  visibility: visible !important;
}
#nwg-modal-container {
  bottom: 0;
  left: 0;
  outline: 0 none;
  /*overflow-x: auto;*/
  /*overflow-y: scroll;*/
  position: fixed;
  right: 0;
  top: 0;
  z-index: 1050;
  visibility: hidden;
}
.nwg-modal {
  position: absolute;
  background: #fff;
  top: 24px;
  left: 12px;
  right: 12px;
  /*bottom: 12px;*/
  margin: auto;
  /*top: 10%;*/
  /*left: 50%;*/
  /*margin: 0 0 30px -218px;*/
  /*width: 436px;*/
  opacity: 0;
  filter: alpha(opacity=0);
  visibility: hidden;
}
/*.nwg-modal.has-img {
  margin: 0 0 0 -68px;
}*/
.nwg-modal .nwg-modal-close {
  float: right;
  color: #fff;
  cursor: pointer;
  font-size: 20px;
  width: 44px;
  height: 44px;
  line-height: 44px;
  text-align: center;
}
.nwg-modal .nwg-modal-image {
  background: none repeat scroll 0 0 #FFFFFF;
  height: 100%;
  position: absolute;
  right: 100%;
  top: 0;
  width: 300px;
  display: none;
}
/*.nwg-modal .nwg-modal-image {
  display: block;
}*/
/*.nwg-modal .nwg-modal-image:after {
  content: '';
  height: 100%;
  display: inline-block;
  vertical-align: middle;
}*/
/*.nwg-modal .nwg-modal-image img {
  vertical-align: middle;
}*/
.nwg-modal .nwg-modal-content {
  /*background: url(../images/new-strip-bg.png) center center no-repeat;*/
  background-color: #6D3257;
  padding: 24px;
  /*min-height: 560px;*/
  -webkit-box-sizing: border-box;
     -moz-box-sizing: border-box;
          box-sizing: border-box;
}
/*.nwg-modal .nwg-modal-content:before {
  background: url("../images/left-tip-welcome.png") no-repeat scroll 0 0;
  content: "";
  height: 170px;
  left: -13px;
  position: absolute;
  top: -26px;
  width: 120px;
}*/
.nwg-modal .nwg-modal-content p {
  color: #fff;
  font-size: 16px;
  line-height: 24px;
  margin: 0 0 16px;
  padding: 0;
}
.nwg-modal .nwg-modal-content .nwg-modal-logo {
  background: url("../images/mtto-logo-white.png") no-repeat scroll center center;
  height: 32px;
  background-size: auto 32px;
  margin: 0 auto 24px;
  padding: 0;
  /*width: 284px;*/
}
.nwg-modal .nwg-modal-actions {
  /*padding: 0 0;*/
}
.nwg-modal label {
  color: #FFFFFF;
  font-size: 15px;
  font-weight: 700;
}
.nwg-modal .nwg-button {
  display: inline-block;
  height: 52px;
  line-height: 52px;
  width: 100%;
  border: 0 none;
  border-radius: 2px;
  background-color: #8b4370;
  text-align: center;
  color: #fff;
  cursor: pointer;
  font-size: 16px;
  font-weight: 700;
}
.nwg-modal .nwg-form-control {
  border-radius: 2px;
  border: 0 none;
  background: #fff;
  margin-bottom: 20px;
  width: 100%;
  height: 52px;
  line-height: 52px;
  padding: 0 12px;
  font-size: 16px;
  color: #555555;
  -webkit-box-sizing: border-box;
     -moz-box-sizing: border-box;
          box-sizing: border-box;
}
.nwg-modal .nwg-form-control.error {
  border: 1px solid #ff0000;
  color: #ff0000;
  font-weight: normal;
}
.nwg-show-modal {
  opacity: 1;
  filter: alpha(opacity=100);
  visibility: visible !important;
}


body > .ui-page.ui-page-theme-a.ui-page-active {
  display: flex;
  min-height: 100vh;
  flex-direction: column;
}

.middle-container {
  flex: 1;
}