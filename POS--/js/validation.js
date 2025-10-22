;(function () {
  function setInvalid(el, message) {
    el.setCustomValidity(message || '')
    if (message) el.classList.add('is-invalid')
    else el.classList.remove('is-invalid')
  }

  document.addEventListener('input', function (e) {
    var t = e.target
    if (t.matches('input[required], textarea[required], select[required]')) {
      if (!t.value.trim())
        setInvalid(t, t.getAttribute('data-required-msg') || 'Required')
      else setInvalid(t, '')
    }
    if (t.type === 'email') {
      var ok = /^\S+@\S+\.\S+$/.test(t.value)
      setInvalid(
        t,
        ok || !t.value
          ? ''
          : t.getAttribute('data-email-msg') || 'Invalid email'
      )
    }
  })
})()

