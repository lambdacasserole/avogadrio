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

# Sets the query string on the page without reloading it.
#
# @param [string] str the new query string
#
window.setQueryString = (str) ->
  if history.pushState
    url = window.location.protocol + '//' + window.location.host + window.location.pathname + '?' + str
    window.history.pushState { path: url }, '', url

# Sets the query string on the page without reloading it, according to the properties of an object.
#
# @param [object] params  the object containing the keys and values to use
#
window.setQueryParams = (params) ->
  str = ''
  for k, v of params
    if v == '' then continue
    if str != '' then str += '&'
    str += "#{k}=#{v}"
  setQueryString str
