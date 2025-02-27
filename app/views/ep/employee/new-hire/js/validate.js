function validateSSN(field) {
    var ssn = $('#' + field);

    var patt = new RegExp("\d{3}[\-]\d{2}[\-]\d{4}");
    var res = patt.test(ssn.val());
    if (!res) {
        ssn.val(ssn.val()
            .match(/\d*/g).join('')
            .match(/(\d{0,3})(\d{0,2})(\d{0,4})/).slice(1).join('-')
            .replace(/-*$/g, ''));
    }

    if (!isValidSSN(ssn.val())) {
      ssn.val('');
      ssn.addClass('invalid');
    } else {
      ssn.removeClass('invalid');
      ssn.addClass('valid');
    }
}

function isValidSSN(str)
{
    regexp = /^(?!000|666)[0-8][0-9]{2}-(?!00)[0-9]{2}-(?!0000)[0-9]{4}$/;

    if (regexp.test(str))
    {
        return true;
    }
    else
    {
        return false;
    }
}

function validatePhone(field) {
    var phone = $('#' + field);

    var patt = new RegExp("\d{3}[\-]\d{3}[\-]\d{4}");
    var res = patt.test(phone.val());
    if (!res) {
        phone.val(phone.val()
            .match(/\d*/g).join('')
            .match(/(\d{0,3})(\d{0,3})(\d{0,4})/).slice(1).join('-')
            .replace(/-*$/g, ''));
    }

    if (!isValidPhone(phone.val())) {
        phone.val('');
        phone.addClass('invalid');
    } else {
        phone.removeClass('invalid');
        phone.addClass('valid');
    }
}

function isValidPhone(str)
{
    regexp = /^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/;

    if (regexp.test(str))
    {
        return true;
    }
    else
    {
        return false;
    }
}

function validateZipCode(field) {
    var zipcode = $('#' + field);

    if (!isValidZipCode(zipcode.val())) {
        zipcode.val('');
        zipcode.addClass('invalid');
    } else {
        zipcode.removeClass('invalid');
        zipcode.addClass('valid');
    }
}

function isValidZipCode(str)
{
    regexp = /(^\d{5}$)/;

    if (regexp.test(str))
    {
        return true;
    }
    else
    {
        return false;
    }
}

function validateBirthDate(field) {
    var datefield = $('#' + field);

    if (dateAge(datefield.val()) < 18) {
        datefield.val('');
        datefield.addClass('invalid');
    } else if (dateAge(datefield.val()) > 99) {
        datefield.val('');
        datefield.addClass('invalid');
    } else {
        datefield.removeClass('invalid');
        datefield.addClass('valid');
    }
}

function validateExpireDate(field) {
    var datefield = $('#' + field);

    if (dateAge(datefield.val()) < 0) { // farther away below zero the father in the future the date is
        datefield.removeClass('invalid');
        datefield.addClass('valid');
    } else {
        datefield.val('');
        datefield.addClass('invalid');
    }
}

function dateAge(datestr, type = 1) {
    var dateval = new Date(datestr);
    var today = new Date();
    today.setHours(0,0,0, 0); // make it so time is not a factor

    var diff_ms = today.getTime() - dateval.getTime();
    var age_dt = new Date(diff_ms);

    var age_dc = (diff_ms / (1000*60*60*24) / 365.25).toFixed(3);
    var age_year = Math.abs(age_dt.getUTCFullYear() - 1970);

    if (type == 1) { // decimal age, based on 365.25
        return age_dc;
    } else if (type == 2) { // year only
        return age_year;
    } else {
        return 0;
    }

}