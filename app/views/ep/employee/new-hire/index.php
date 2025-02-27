<?php

$states = array(
    'AL'=>'Alabama',
    'AK'=>'Alaska',
    'AZ'=>'Arizona',
    'AR'=>'Arkansas',
    'CA'=>'California',
    'CO'=>'Colorado',
    'CT'=>'Connecticut',
    'DE'=>'Delaware',
    'DC'=>'District of Columbia',
    'FL'=>'Florida',
    'GA'=>'Georgia',
    'HI'=>'Hawaii',
    'ID'=>'Idaho',
    'IL'=>'Illinois',
    'IN'=>'Indiana',
    'IA'=>'Iowa',
    'KS'=>'Kansas',
    'KY'=>'Kentucky',
    'LA'=>'Louisiana',
    'ME'=>'Maine',
    'MD'=>'Maryland',
    'MA'=>'Massachusetts',
    'MI'=>'Michigan',
    'MN'=>'Minnesota',
    'MS'=>'Mississippi',
    'MO'=>'Missouri',
    'MT'=>'Montana',
    'NE'=>'Nebraska',
    'NV'=>'Nevada',
    'NH'=>'New Hampshire',
    'NJ'=>'New Jersey',
    'NM'=>'New Mexico',
    'NY'=>'New York',
    'NC'=>'North Carolina',
    'ND'=>'North Dakota',
    'OH'=>'Ohio',
    'OK'=>'Oklahoma',
    'OR'=>'Oregon',
    'PA'=>'Pennsylvania',
    'RI'=>'Rhode Island',
    'SC'=>'South Carolina',
    'SD'=>'South Dakota',
    'TN'=>'Tennessee',
    'TX'=>'Texas',
    'UT'=>'Utah',
    'VT'=>'Vermont',
    'VA'=>'Virginia',
    'WA'=>'Washington',
    'WV'=>'West Virginia',
    'WI'=>'Wisconsin',
    'WY'=>'Wyoming',
);

?>

<?=includePageScript($data['page']['slug'], 'validate.js');?>

<style>
    .valid{
        border:1px solid blue;
    }
    .invalid{
        border:1px solid red;
    }
</style>

<script type="text/javascript">
  function selectLanguage(language) {
      $('#middle-initial-row').show();
      $('.english-labels').show();
      $('.spanish-labels').hide();
      if (language == 'es') {
          $('#middle-initial-row').hide();
          $('.spanish-labels').show();
          $('.english-labels').hide();
      }
      $('#main-data').show();
      $('#submit-btn').show();

      //$('#lang-question').hide();
  }
</script>

<div class="main">
    <h2 class="page-header">Employee New Hire</h2>
    <div class="panel">
        <div class="panel-body">
            <div class="col-md-6">
              <div id="lang-question" style="padding-bottom: 25px;"><span style="font-family: Arial; font-size: 1.1em; font-weight: bold;">What is your native language? ¿Cual es tu primer idioma?</span>&nbsp;&nbsp;
                <button class="btn btn-primary" style="padding: 5px 10px 5px 10px;" onclick="selectLanguage('en');">English</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class="btn btn-success" style="padding: 5px 10px 5px 10px;" onclick="selectLanguage('es');">Español</button>
              </div>
              <form method="POST" action="/employee/new-hire/submit">
                <table id="main-data" style="font-family: Arial; font-size: 1.3em; display: none;" class="table table-striped table-responsive table-condensed table-hover no-margin">
                  <tr>
                    <td class="text-nowrap"><strong><span class="english-labels">First Name:<br/></span><span class="spanish-labels">Premire nombre:</span></strong></td>
                    <td><input  name="first_name" id="first-name" type="text" size="35" required title="Enter your first name" style="text-transform:capitalize;"/></td>
                  </tr>
                  <tr id="middle-initial-row">
                    <td class="text-nowrap"><strong><span class="english-labels">Middle Initial:<br/></span><span class="spanish-labels">Inicial:</span></strong></td>
                    <td><input name="middle_initial" id="middle-initial" type="text" title="Enter your first name" size="3" style="text-transform:uppercase;"/></td>
                  </tr>
                  <tr>
                    <td class="text-nowrap"><strong><span class="english-labels">Last Name:<br/></span><span class="spanish-labels">Apellido:</span></strong></td>
                    <td><input name="last_name" id="last-name" type="text" required title="Enter your last name" size="35" style="text-transform:capitalize;"/></td>
                  </tr>
                  <tr>
                    <td class="text-nowrap"><strong><span class="english-labels">Preferred First Name:<br/></span><span class="spanish-labels">Nombre preferido:</span></strong></td>
                    <td><input name="preferred_name" id="preferred-name" type="text" title="Enter the first name you prefer to use" size="35" style="text-transform:capitalize;"/></td>
                  </tr>
                  <tr>
                    <td class="text-nowrap"><strong><span class="english-labels">Date of Birth:<br/></span><span class="spanish-labels">Fecha de nacimiento:</span></strong></td>
                    <td><input name="date_of_birth" id="date-of-birth" type="date" required size="15" onblur="validateBirthDate('date-of-birth');"/></td>
                  </tr>
                  <tr>
                    <td class="text-nowrap"><strong><span class="english-labels">Address:<br/></span><span class="spanish-labels">Dirección:</span></strong></td>
                    <td><input name="address" id="address" type="text" required size="55" style="text-transform:capitalize;"/></td>
                  </tr>
                  <tr>
                    <td class="text-nowrap"><strong><span class="english-labels">City:<br/></span><span class="spanish-labels">Ciudad:</span></strong></td>
                    <td><input name="city" id="city" type="text" required size="35" style="text-transform:capitalize;"/></td>
                  </tr>
                  <tr>
                    <td class="text-nowrap"><strong><span class="english-labels">State:<br/></span><span class="spanish-labels">Estado:</span></strong></td>
                    <td>
                      <select name="state" id="state" required>
                          <?php
                            foreach ($states as $state=>$name) {
                              echo "<option value=\"$state\"",  ($state=='AZ')?'selected':'', ">$name</option>";
                            }
                          ?>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td class="text-nowrap"><strong><span class="english-labels">Zip Code:<br/></span><span class="spanish-labels">Código postal:</span></strong></td>
                    <td><input name="zipcode" id="zipcode" type="text" inputmode="numeric" pattern="[0-9]{5}" required  placeholder="12345" size="10" title="Enter 5 digit zip code only" onblur="validateZipCode('zipcode');"/></td>
                  </tr>
                  <tr>
                    <td class="text-nowrap"><strong><span class="english-labels">Home Phone#:<br/></span><span class="spanish-labels">Telefono de casa:</span></strong></td>
                    <td><input name="home_phone" id="home-phone" type="tel"  placeholder="480-555-1234" size="20" onblur="validatePhone('home-phone');"/></td>
                  </tr>
                  <tr>
                    <td class="text-nowrap"><strong><span class="english-labels">Cell Phone#:<br/></span><span class="spanish-labels">Número de telefono célular:</span></strong></td>
                    <td><input name="cell_phone" id="cell-phone" type="tel" placeholder="480-555-1234" required size="20" onblur="validatePhone('cell-phone');"/></td>
                  </tr>

                  <tr>
                    <td class="text-nowrap"><strong><span class="english-labels">Social Security#:<br/></span><span class="spanish-labels">Número de seguro social:</span></strong></td>
                    <td><input name="ssn" id="ssn" required title="Enter your Social Security Number" placeholder="123-45-6789" size="15" onblur="validateSSN('ssn');"/></td>
                  </tr>
                  <tr>
                    <td class="text-nowrap"><strong><span class="english-labels">Drivers License#:<br/></span><span class="spanish-labels">Número de licencia:</span></strong></td>
                    <td><input name="drivers_license" id="drivers-license" size="15" style="text-transform:uppercase;"/></td>
                  </tr>
                  <tr>
                    <td class="text-nowrap"><strong><span class="english-labels">Drivers License State:<br/></span><span class="spanish-labels">Estado licencia:</span></strong></td>
                    <td>
                      <select name="drivers_license_state" id="drivers-license-state">
                          <?php
                          foreach ($states as $state=>$name) {
                              echo "<option value=\"$state\"",  ($state=='AZ')?'selected':'', ">$name</option>";
                          }
                          ?>
                      </select>
                  </tr>
                  <tr>
                    <td class="text-nowrap"><strong><span class="english-labels">Drivers License Expiration:<br/></span><span class="spanish-labels">Fecha de vencimiento:</span></strong></td>
                    <td><input name="drivers_license_expire" id="drivers-license-expire"  type="date" size="15" onblur="validateExpireDate('drivers-license-expire');"/></td>
                  </tr>
                </table>
                <br/>
                <input id="submit-btn" class="btn btn-primary" type="submit" name="submit" style="display: none;">
              </form>
            </div>
        </div>
    </div>
</div>
