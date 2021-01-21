import $ from 'jquery'
// import 'jquery-easing/jquery.easing.1.3'
// import 'lazysizes'

import ComponentExample from './components/c-example'

export const Site = {}

window.isPageLoaded = false
window.isPageReady = false

$(window).on('load', () => {
  window.isPageLoaded = true
})

$(function () {
  Site.variables = {
    window: $(window),
    document: $(document),
    body: $('body'),
    head: $('head'),
    htmlBody: $('html, body'),
    mainWrapper: $('#main-wrapper')
  }

  const s = Site.variables

  window.isPageReady = true

  ComponentExample(s)

  // Debug
  if (window.location.hostname === 'localhost' || window.location.hostname.endsWith('.test')) {
    s.body.addClass('debug')
  }
})

export default Site
