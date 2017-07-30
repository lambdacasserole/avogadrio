$(document).ready ->
  screenWidth = 1920
  screenHeight = 1280

  foregroundColor = 'ffffff'
  backgroundColor = '000000'

  previewElement = $ 'body'
  compoundTextBox = $ '.comp-name'
  
  # URL builder functions for API.
  
  buildUrl = (width, height, foreground, background, name) ->
    "/api/name/#{width}/#{height}/#{background}/#{foreground}/#{name}"

  buildMoleculeOnlyUrl = (width, height, foreground, name) ->
    "/api/name/#{width}/#{height}/#{foreground}/#{name}"

  # Actions to take when we refresh the preview.
    
  refreshPreview = ->
    url = buildMoleculeOnlyUrl screenWidth, screenHeight, foregroundColor, compoundTextBox.val()
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
    refreshPreview() 
   
  # Download button should open rendered image for download.
   
  $('.download-btn').on 'click', (e) ->
    url = buildUrl screenWidth, screenHeight, foregroundColor, backgroundColor, compoundTextBox.val()
    window.open url
    
  refreshPreview() # Initial update.
