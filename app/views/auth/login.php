<form id="login-form" action="" method="post">
  <input type="hidden" name="action" value="user-login">
  <input type="hidden" name="user_type" value="<?=config('app.user_type');?>">
  <input type="hidden" name="user_ipv4" value="<?=$_SERVER['REMOTE_ADDR'];?>">
  <h4 class="txt-center padding10"> Please log in to your account.</h4>
  <div class="form-group">
    <input type="text" id="user-name" name="user_name" class="form-control" placeholder="User Name" required autofocus/>
  </div>
  <div class="form-group">
    <input type="password" id="user-password" name="user_password" class="form-control" placeholder="Password" required/>
  </div>
  <div class="form-group">
    <div class="pull-left no-padding">
      <input type="checkbox" id="remember-me" name="remember_me" value="true"> Remember me
    </div>
    <div class="pull-right no-padding txt-right">
      <a href="/auth/forgot-password" class="primary" role="button">Forgot Password?</a>
    </div>
    <br>
  </div>
  <div class="form-group">
    <button class="btn btn-primary btn-block btn-lg" type="submit" id="submit-btn"><i class="fas fa-sign-in-alt fa-fw"></i> Log In</button>
  </div>
  <div id="response-message"></div>
</form>

<script>
  $(function () {
    const form = $('#login-form')

    form.parsley().on('field:validated', function () {
      let ok = $('.parsley-error').length === 0
    })

    form.submit(function (e) {

      let form_data = false
      if (window.FormData) {
        form_data = new FormData(form[0])
      }

      $('#submit-btn').prop('disabled', true).html('<i class="fas fa-cog fa-spin"></i> &nbsp; Authorizing')

      $.ajax({
        type: 'post',
        url: '/auth/login',
        cache: false,
        data: form_data ? form_data : form.serialize(),
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function (response) {
          if (response.message === 'OK') {
            $('#response-message').removeClass('alert alert-danger').addClass('alert alert-success').html('<i class="fas fa-check"></i> ' + response.message)
            $('#login-btn').html('<i class="fas fa-cog fa-spin"></i> &nbsp; Logging In')
            setTimeout(function () {
              window.location.href = '/'
            }, 200)
          } else {
            form.effect('shake')
            $('#response-message').addClass('alert alert-danger').html('<i class="fa fa-exclamation-triangle"></i> ' + response.message)
            $('#submit-btn').prop('disabled', false).html('<i class="fas fa-sign-in-alt fa-fw"></i> Log In')
          }
        },
      })
      e.preventDefault()
    })
  })
</script>
