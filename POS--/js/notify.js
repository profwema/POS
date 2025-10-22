;(function () {
  window.notify = {
    success: function (msg) {
      try {
        $.gritter.add({
          title: 'Success',
          text: msg,
          class_name: 'gritter-success',
        })
      } catch (e) {
        alert(msg)
      }
    },
    error: function (msg) {
      try {
        $.gritter.add({
          title: 'Error',
          text: msg,
          class_name: 'gritter-error',
        })
      } catch (e) {
        alert(msg)
      }
    },
    info: function (msg) {
      try {
        $.gritter.add({ title: 'Info', text: msg, class_name: 'gritter-info' })
      } catch (e) {
        alert(msg)
      }
    },
  }
})()

