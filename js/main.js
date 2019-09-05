import $ from 'jquery';
import 'jquery-easing/jquery.easing.1.3';

const Site = {};

$(document).ready(() => {
  Site.vars = {
    window:   $(window),
    document: $(document),
    body:     $('body'),
    htmlBody: $('html, body'),
    header:   $('#header'),
    footer:   $('#footer')
  };

  const s = Site.vars;


  // Debug
  if (window.location.hostname === 'localhost' || window.location.hostname.endsWith('.test')) {
    s.body.addClass('debug');
  }
});

export default Site;
