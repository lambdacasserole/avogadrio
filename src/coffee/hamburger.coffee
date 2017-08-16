# Copyright (c) 2017 by Jay Holtslander (http://codepen.io/j_holtslander/pen/XmpMEp)
#
# Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated 
# documentation files (the "Software"), to deal in the Software without restriction, including without limitation the 
# rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit
# persons to whom the Software is furnished to do so, subject to the following conditions:
#
# The above copyright notice and this permission notice shall be included in all copies or substantial portions of the 
# Software.
#
# THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE 
# WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR 
# COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR 
# OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
#
# Converted to CoffeeScript and modified by Saul Johnson (@lambdacasserole).

$(document).ready ->
  trigger = $ '.hamburger'
  wrapper = $ '#wrapper'
  isClosed = false

  hamburger_cross = ->
    if isClosed == true
      trigger.removeClass 'is-open'
      trigger.addClass 'is-closed'
      isClosed = false
    else
      trigger.removeClass 'is-closed'
      trigger.addClass 'is-open'
      isClosed = true

  toggle_open = ->
    wrapper.toggleClass 'toggled'
    hamburger_cross()

  trigger.click ->
    hamburger_cross()
    
  $('[data-toggle="offcanvas"]').click ->
    wrapper.toggleClass 'toggled'

  # Expand burger menu if URL asks for it.
  if getParameterByName('showmenu') == 'true'
    toggle_open()
