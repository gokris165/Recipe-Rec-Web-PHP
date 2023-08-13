// Validation function for the login page.
function validate_login_page(form)
{
    fail = validate_username(form.user.value);
    fail += validate_login_password(form.pass.value);
    
    if(fail == "")
        return true;
    else
    {
        alert(fail);
        return false;
    }
}

// Validation function for the signup page.
function validate_signup_page(form)
{
    fail = validate_username(form.user.value);
    fail += validate_signup_password(form.pass.value);
    fail += validate_email(form.email.value);
    
    if(fail == "")
        return true;
    else
    {
        alert(fail);
        return false;
    }
}

// Validation function for the enter record page.
function validate_enter_record_page(form)
{
    fail = validate_filename(form.name.value);
    
    if(fail == "")
        return true;
    else
    {
        alert(fail);
        return false;
    }
}

// The password in the login page cannot be an empty field.
function validate_login_password(field)
{
    return (field == "") ? "Password field cannot be empty.\n" : "";
}

// The filename entered in the enter record page cannot be empty.
function validate_filename(field)
{
    return (field == "") ? "Name field cannot be empty.\n" : "";
}

// The username entered in the login page cannot be empty.
function validate_username(field)
{
    return (field == "") ? "Username field cannot be empty.\n" : "";
}

// The password entered during the signup process cannot be empty and 
// must contain 1 uppercase, 1 lowercase, and 1 number.
function validate_signup_password(field)
{
    if (field == "") 
        return "Password field cannot be empty.\n";
    else if (field.length < 4)
        return "Passwords must be atleast 4 characters long.\n";
    else if (!/[a-z]/.test(field) || !/[A-Z]/.test(field) || !/[0-9]/.test(field))
        return "Password has to contain atleast 1 uppercase, 1 lowercase, and 1 number.\n";
    else 
        return "";
}

// The email entered during the signup process must have exactly 1 '.' and '@' symbol.
function validate_email(field)
{
    if (field == "")
        return "Email field cannot be empty.\n";
    else if (!((field.indexOf(".") > 0) && 
               (field.indexOf("@") > 0) && 
               ((field.match(/@/g) || []).length == 1) && 
               ((field.match(/\./g) || []).length == 1)) ||
               (/[^a-zA-Z0-9.@_-]/.test(field)))
        return "The entered email is invalid.\n";
    return "";
}
