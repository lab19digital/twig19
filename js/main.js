import $ from 'jquery';
import 'jquery-easing/jquery.easing.1.3';
// import 'waypoints/lib/jquery.waypoints.min';

// import Animations from './components/animations';
// import ScrollTo from './components/scroll-to';

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


  // Animations(s);
  // ScrollTo(s, id);


  // Debug
  if (window.location.hostname === 'localhost' || window.location.hostname.endsWith('.test')) {
    s.body.addClass('debug');
  }
});

export default Site;
