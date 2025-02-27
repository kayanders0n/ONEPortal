<form id="passcode-form" action="" method="post">
  <input type="hidden" name="action" value="validate-passcode">
  <input type="hidden" name="user_type" value="<?=config('app.user_type');?>">
  <input type="hidden" name="user_ipv4" value="<?=$_SERVER['REMOTE_ADDR'];?>">
  <input type="hidden" name="user_id" value="<?=$_GET['i'];?>">
  <input type="hidden" name="token" value="<?=$_GET['t'];?>">
  <h4 class="txt-center padding10"> Enter validation code </h4>
  <div class="form-group">
    <input type="text" id="passcode" name="passcode" class="form-control txt-center" placeholder="_____________" required autofocus style="letter-spacing: 5px"/>
  </div>
  <div class="form-group">
    <button class="btn btn-primary btn-block btn-lg" type="submit" id="submit-btn">Validate Code</button>
  </div>
  <div id="response-message"></div>
  <div class="txt-center">An email was sent containing your validation code.<br> Please check your email.</div>
</form>

<script>
  $(function () {
    const form = $('#passcode-form')

    form.parsley().on('field:validated', function () {
      let ok = $('.parsley-error').length === 0
    })

    form.submit(function (e) {

      let form_data = false
      if (window.FormData) {
        form_data = new FormData(form[0])
      }

      $('#submit-btn').prop('disabled', true).html('<i class="fas fa-cog fa-spin"></i> &nbsp; Validating')

      $.ajax({
        type: 'post',
        url: '/auth/validate-passcode',
        cache: false,
        data: form_data ? form_data : form.serialize(),
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function (response) {
          if (response.message === 'OK') {
            $('#response-message').removeClass('alert alert-danger').addClass('alert alert-success').html('<i class="fas fa-check"></i> ' + response.message)
            $('#submit-btn').html('<i class="fas fa-cog fa-spin"></i> &nbsp; Redirecting')
            setTimeout(function () {
              window.location.href = '/auth/reset-password?i=' + response.user_id + '&t=' + response.token
            }, 200)
          } else {
            form.effect('shake')
            $('#response-message').addClass('alert alert-danger').html('<i class="fa fa-exclamation-triangle"></i> ' + response.message)
            $('#submit-btn').prop('disabled', false).html('Validate Code')
          }
        }
      })
      e.preventDefault()
    })
  })
</script>
