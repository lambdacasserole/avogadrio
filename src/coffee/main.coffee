$(document).ready ->
  # Screen dimensions.
  screenWidth = window.screen.width
  screenHeight = window.screen.height

  # Wallpaper color attributes.
  foregroundColor = 'ce3838'
  backgroundColor = '5f0000'

  # Custom label for molecule.
  customLabel = ''

  # Data about current molecule.
  smilesMode = true
  currentCompoundName = ''
  currentCompoundSmiles = 'CCN(CC)C1=CC2=C(C=C1)N=C3C4=CC=CC=C4C(=O)C=C3O2'

  # Text entry fields for molecules.
  compoundTextBox = $ '.comp-name'
  smilesTextBox = $ '.comp-smiles'
  customLabelTextBox = $ '.cust-lbl-tbox'

  # Element to use for wallpaper preview.
  previewElement = $ 'body, html'
  
  # Download button element.
  downloadButton = $ '.download-btn'

  # Error message elements.
  errorRows = $ '.row-error'
  invalidCompoundNameMessage = $ '.row-error-compound'
  invalidCompoundSmilesMessage = $ '.row-error-smiles'

  # Gets the sanitized compound name as entered by the user.
  #
  getCompoundName = ->
    encodeURIComponent compoundTextBox.val().replace(/\s/g, '')

  # Gets the sanitized compound SMILES structure as entered by the user.
  #
  getCompoundSmiles = ->
    encodeURIComponent smilesTextBox.val()

  # Gets the sanitized custom molecule label as entered by the user.
  #
  getCustomLabel = ->
    encodeURIComponent customLabelTextBox.val()

  # URL builder functions for API.

  # Builds a URL to generate a wallpaper image from a compound name.
  #
  # @param [int] width          the width of the image
  # @param [int] height         the height of the image
  # @param [string] foreground  the molecule color (hex, without `#`)
  # @param [string] background  the background color (hex, without `#`)
  # @param [name] name          the compound name
  #
  buildUrl = (width, height, foreground, background, name) ->
    url = "/api/name/#{width}/#{height}/#{background}/#{foreground}/#{name}"
    if customLabel != '' then url += "?label=#{customLabel}"
    return url

  # Builds a URL to generate a molecule-only image from a compound name.
  #
  # @param [int] width          the width of the image if it were rendered as a wallpaper
  # @param [int] height         the height of the image if it were rendered as a wallpaper
  # @param [string] foreground  the molecule color (hex, without `#`)
  # @param [name] name          the compound name
  #
  buildMoleculeOnlyUrl = (width, height, foreground, name) ->
    url = "/api/name/#{width}/#{height}/#{foreground}/#{name}"
    if customLabel != '' then url += "?label=#{customLabel}"
    return url

  # Builds a URL to generate a wallpaper image from a SMILES structure.
  #
  # @param [int] width          the width of the image
  # @param [int] height         the height of the image
  # @param [string] foreground  the molecule color (hex, without `#`)
  # @param [string] background  the background color (hex, without `#`)
  # @param [name] name          the SMILES structure
  #
  buildSmilesUrl = (width, height, foreground, background, smiles) ->
    url = "/api/smiles/#{width}/#{height}/#{background}/#{foreground}/#{smiles}"
    if customLabel != '' then url += "?label=#{customLabel}"
    return url

  # Builds a URL to generate a molecule-only image from a SMILES structure.
  #
  # @param [int] width          the width of the image if it were rendered as a wallpaper
  # @param [int] height         the height of the image if it were rendered as a wallpaper
  # @param [string] foreground  the molecule color (hex, without `#`)
  # @param [name] smiles        the SMILES structure
  #
  buildSmilesMoleculeOnlyUrl = (width, height, foreground, smiles) ->
    url = "/api/smiles/#{width}/#{height}/#{foreground}/#{smiles}"
    if customLabel != '' then url += "?label=#{customLabel}"
    return url

  # Checks if a compound name can be converted to SMILES using the configured database.
  #
  # @param [string] name      the compound name
  # @param [function] success the success callback
  # @param [function] fail    the failure callback
  #
  checkMoleculeName = (name, success, fail) ->
    $.get "/api/name/exists/#{name}", (data) ->
      if data then success() else fail()

  # Checks if a string is a valid SMILES structure.
  #
  # @param [string] name      the compound name
  # @param [function] success the success callback
  # @param [function] fail    the failure callback
  #
  checkSmiles = (smiles, success, fail) ->
    regex = /^([^J][a-z0-9@+\-\[\]\(\)\\\/%=#$]{0,})$/ig
    if regex.test smiles then success() else fail()

  # Checks if a string is a valid hex color.
  #
  # @param [string] color the color to check
  #
  checkColor = (color) ->
    regex = /^(([0-9a-fA-F]{2}){3}|([0-9a-fA-F]){3})$/ig
    regex.test color

  # Actions to take when we refresh the preview.

  # Shows the invalid compound name error message.
  #
  failPreview = ->
    invalidCompoundNameMessage.show()

  # Shows the invalid smiles structure error message.
  #
  failPreviewSmiles = ->
    invalidCompoundSmilesMessage.show()

  # Updates the download link according to the currently displayed molecule.
  #
  updateDownloadLink = ->
    url = buildUrl screenWidth, screenHeight, foregroundColor, backgroundColor, currentCompoundName
    if smilesMode
      url = buildSmilesUrl screenWidth, screenHeight, foregroundColor, backgroundColor, currentCompoundSmiles
    downloadButton.attr 'download', if smilesMode then 'smiles_molecule' else currentCompoundName
    downloadButton.attr 'href', url
    
  # Updates the displayed preview.
  #
  # @param [object] element     the element to update
  # @param [string] url         the URL of the image to preview
  # @param [string] background  the background color (hex, without `#`)
  #
  updatePreview = (element, url, background) ->
    element.css 'background', "url('#{url}')"
    element.css 'background-color', "##{background}"
    element.css 'background-position', '50% 50%'
    element.css 'background-repeat', 'no-repeat'
    updateDownloadLink()

  # Refreshes the preview using the compound name text box.
  #
  refreshPreviewCompoundName = ->
    currentCompoundName = getCompoundName()
    smilesMode = false
    url = buildMoleculeOnlyUrl screenWidth, screenHeight, foregroundColor, currentCompoundName
    updatePreview previewElement, url, backgroundColor

  # Refreshes the preview using the SMILES structure text box.
  #
  refreshPreviewSmiles = ->
    currentCompoundSmiles = getCompoundSmiles()
    smilesMode = true
    url = buildSmilesMoleculeOnlyUrl screenWidth, screenHeight, foregroundColor, currentCompoundSmiles
    updatePreview previewElement, url, backgroundColor

  # Refreshes the preview using the compound name or SMILES structure text box depending on mode.
  #
  modeAwareRefreshPreview = ->
    if smilesMode then refreshPreviewSmiles() else refreshPreviewCompoundName()

  passedForeground = getParameterByName 'foreground'
  if passedForeground != null && checkColor(passedForeground)
    foregroundColor = passedForeground

  passedBackground = getParameterByName 'background'
  if passedBackground != null && checkColor(passedBackground)
    backgroundColor = passedBackground

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
    modeAwareRefreshPreview()

  $('.picker-bg').on 'change', (e) ->
    backgroundColor = $(e.target).val().substring(1)
    modeAwareRefreshPreview()
    
  # Compound name update button should refresh the preview.
    
  $('.update-btn').on 'click', (e) ->
    errorRows.hide()
    checkMoleculeName getCompoundName(), refreshPreviewCompoundName, failPreview

  $('.update-smiles-btn').on 'click', (e) ->
    smilesMode = true
    errorRows.hide()
    checkSmiles getCompoundSmiles(), refreshPreviewSmiles, failPreviewSmiles

  # Label update button should also refresh preview.

  $('.update-lbl-btn').on 'click', (e) ->
    customLabel = getCustomLabel()
    modeAwareRefreshPreview()

  # Put passed parameter values into text boxes if needed.

  passedCompoundName = getParameterByName 'compound'
  if passedCompoundName != null
    compoundTextBox.val(passedCompoundName)
    smilesMode = false

  passedSmiles = getParameterByName 'smiles'
  if passedSmiles != null
    smilesTextBox.val(passedSmiles)
    smilesMode = true

  passedLabel = getParameterByName 'label'
  if passedLabel != null
    customLabelTextBox.val(passedLabel)

  # Grab initial values from text boxes.

  currentCompoundName = compoundTextBox.val()
  currentCompoundSmiles = smilesTextBox.val()
  customLabel = customLabelTextBox.val()

  # Initial update.
  modeAwareRefreshPreview()
