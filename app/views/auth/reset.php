<form id="reset-form" action=""
      data-parsley-excluded="input[type=button], input[type=submit], input[type=hidden], [disabled], :hidden"
      data-parsley-trigger="keyup"
      data-parsley-validate>
  <input type="hidden" name="action" value="user-login">
  <input type="hidden" name="user_type" value="<?=config('app.user_type');?>">
  <input type="hidden" name="user_ipv4" value="<?=$_SERVER['REMOTE_ADDR'];?>">
  <input type="hidden" name="user_id" value="<?=$_GET['i'];?>">
  <input type="hidden" name="token" value="<?=$_GET['t'];?>">
  <h4 class="txt-center padding10"> Please enter your new password.</h4>
  <div class="form-group">
    <input class="form-control" type="password" id="new-password" name="new_password" placeholder="New Password" tabindex="1" required autofocus minlength="10"
           data-parsley-minlength="10"
           data-parsley-required-message="Please enter your new password."
           data-parsley-uppercase="1"
           data-parsley-lowercase="1"
           data-parsley-number="1"
           data-parsley-special="1"
           data-parsley-required/>
  </div>
  <div class="form-group">
    <input class="form-control" type="password" id="confirm-password" name="confirm_password" placeholder="Confirm Password" tabindex="2" required minlength="10"
           data-parsley-minlength="10"
           data-parsley-required-message="Please re-enter your new password."
           data-parsley-equalto="#new-password"
           data-parsley-required/>
  </div>
  <div class="form-group">
    <button class="btn btn-primary btn-block btn-lg" type="submit" id="submit-btn">Save Password</button>
  </div>
  <div id="response-message"></div>
  <br>
  <div style="width: 260px; margin: 0 auto;">
    <h4 class="txt-center">Password Requirements</h4>
    <div>
      <ul>
        <li>Ten character minimum</li>
        <li>At least one lower case letter</li>
        <li>At least one upper case letter</li>
        <li>At least one number</li>
        <li>Confirm password matches</li>
      </ul>
    </div>
  </div>
</form>

<script>
  $(function () {
    const form = $('#reset-form')

    form.parsley().on('field:validated', function () {
      let ok = $('.parsley-error').length === 0

      //has uppercase
      window.Parsley.addValidator('uppercase', {
        requirementType: 'number',
        validateString: function (value, requirement) {
          var uppercases = value.match(/[A-Z]/g) || []
          return uppercases.length >= requirement
        },
        messages: {
          en: 'Your password must contain at least (%s) uppercase letter.'
        }
      })

      //has lowercase
      window.Parsley.addValidator('lowercase', {
        requirementType: 'number',
        validateString: function (value, requirement) {
          var lowecases = value.match(/[a-z]/g) || []
          return lowecases.length >= requirement
        },
        messages: {
          en: 'Your password must contain at least (%s) lowercase letter.'
        }
      })

      //has number
      window.Parsley.addValidator('number', {
        requirementType: 'number',
        validateString: function (value, requirement) {
          var numbers = value.match(/[0-9]/g) || []
          return numbers.length >= requirement
        },
        messages: {
          en: 'Your password must contain at least (%s) number.'
        }
      })
    })

    form.submit(function (e) {

      let form_data = false
      if (window.FormData) {
        form_data = new FormData(form[0])
      }

      $('#submit-btn').prop('disabled', true).html('<i class="fas fa-cog fa-spin"></i> &nbsp; Processing')

      $.ajax({
        type: 'post',
        url: '/auth/reset-password',
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
              window.location.href = '/auth/login'
            }, 200)
          } else {
            form.effect('shake')
            $('#response-message').addClass('alert alert-danger').html('<i class="fa fa-exclamation-triangle"></i> ' + response.message)
            $('#submit-btn').prop('disabled', false).html('Save Password')
          }
        }
      })
      e.preventDefault()
    })
  })
</script>
