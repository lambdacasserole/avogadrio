# Gets the value of a parameter passed to the page via a query string.
#
# @param [string] name  the name of the parameter to get the value of
# @param [string] url   the URL to parse the query string from, default to `window.location.href`
#
window.getParameterByName = (name, url) ->
  if !url
    url = window.location.href
  name = name.replace(/[\[\]]/g, '\\$&')
  regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)')
  results = regex.exec(url)
  if !results
    return null
  if !results[2]
    return ''
  decodeURIComponent results[2].replace(/\+/g, ' ')
