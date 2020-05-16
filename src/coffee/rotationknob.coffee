###
			 * Demo code for knob element.
###

initRotationKnob = ->
  # Create knob element, 300 x 300 px in size.
  knob = pureknob.createKnob(88, 88)
  # Set properties.
  knob.setProperty 'angleStart', 0
  knob.setProperty 'angleEnd', Math.PI * 2
  knob.setProperty 'colorFG', '#C0C0C0'
  knob.setProperty 'colorBG', '#505050'
  knob.setProperty 'trackWidth', 0.4
  knob.setProperty 'valMin', 0
  knob.setProperty 'valMax', 359
  knob.setProperty 'needle', true
  # Set initial value.
  knob.setValue 0

  knob.addListener window.knobListener
  # Create element node.
  node = knob.node()
  # Add it to the DOM.
  elem = document.getElementById('rotation_knob')
  elem.appendChild node
  window.rotationKnob = knob

window.initRotationKnob = initRotationKnob
