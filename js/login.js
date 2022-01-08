function checkFields() {
    var pass1 = (String)(document.getElementById("pass1").value);
    var pass2 = (String)(document.getElementById("pass2").value);


    if (pass1!== '' && pass1 === pass2) {
        return true;
    }
    alert("Passwords don't match or are empty fields!");
    return false;
}