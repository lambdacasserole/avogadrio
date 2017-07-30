$(document).ready ->
  trigger = $('.hamburger')
  # var overlay = $('.overlay');
  isClosed = false

  hamburger_cross = ->
    if isClosed == true
      # overlay.hide();
      trigger.removeClass 'is-open'
      trigger.addClass 'is-closed'
      isClosed = false
    else
      # overlay.show();
      trigger.removeClass 'is-closed'
      trigger.addClass 'is-open'
      isClosed = true

  trigger.click ->
    hamburger_cross()
    
  $('[data-toggle="offcanvas"]').click ->
    $('#wrapper').toggleClass 'toggled'
