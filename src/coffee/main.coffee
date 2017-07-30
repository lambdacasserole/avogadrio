$(document).ready ->
  screenWidth = window.screen.availWidth
  screenHeight = window.screen.availHeight

  foregroundColor = 'ffffff'
  backgroundColor = '000000'
  compoundName = 'caffeine'

  previewElement = $ 'body'
  compoundTextBox = $ '.comp-name'
  invalidCompoundMessage = $ '.row-error'

  getCompoundName = ->
    encodeURIComponent(compoundTextBox.val()).replace /\s/g, ''
  window.getCompoundName = getCompoundName
  # URL builder functions for API.
  
  buildUrl = (width, height, foreground, background, name) ->
    "/api/name/#{width}/#{height}/#{background}/#{foreground}/#{name}"
    
  buildMoleculeOnlyUrl = (width, height, foreground, name) ->
    "/api/name/#{width}/#{height}/#{foreground}/#{name}"

  checkMoleculeName = (name, success, fail) ->
    $.get "/api/name/exists/#{name}", (data) ->
      if data then success() else fail()
    
  # Actions to take when we refresh the preview.

  failPreview = ->
    invalidCompoundMessage.show()

  refreshPreview = ->
    compoundName = getCompoundName()
    url = buildMoleculeOnlyUrl screenWidth, screenHeight, foregroundColor, getCompoundName()
    previewElement.css 'background', "url(#{url})"
    previewElement.css 'background-color', "##{backgroundColor}"
    previewElement.css 'background-position', '50% 50%'
    previewElement.css 'background-repeat', 'no-repeat'
    
  # Let's initialize the color pickers.
    
  $('.picker-fg').spectrum
    color: "##{foregroundColor}"
    clickoutFiresChange: true
    chooseText: 'Update'
    preferredFormat: 'hex'
    
  $('.picker-bg').spectrum
    color: "##{backgroundColor}"
    clickoutFiresChange: true
    chooseText: 'Update'
    preferredFormat: 'hex'

  # Set up color picker change events.
    
  $('.picker-fg').on 'change', (e) ->
    foregroundColor = $(e.target).val().substring(1)
    refreshPreview()
    
  $('.picker-bg').on 'change', (e) ->
    backgroundColor = $(e.target).val().substring(1)
    refreshPreview()
    
  # Compound name update button should refresh the preview.
    
  $('.update-btn').on 'click', (e) ->
    invalidCompoundMessage.hide()
    checkMoleculeName getCompoundName(), refreshPreview, failPreview
   
  # Download button should open rendered image for download.
   
  $('.download-btn').on 'click', (e) ->
    url = buildUrl screenWidth, screenHeight, foregroundColor, backgroundColor, compoundName
    window.open url
    
  refreshPreview() # Initial update.
