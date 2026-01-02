let inputs = {
  username: new String,
  password: new String,
  remember: false
};

$(document).on('submit', function(e) {
  e.preventDefault();
  let page;
  if ($(this).text() === "Register") {
    page = "register.php";
  } else {
    page = "login.php";
  }

  jQuery.ajax({
    url: "ajax/login/"+page,
    data:'username='+encodeURIComponent(inputs.username)
          +"&password="+encodeURIComponent(inputs.password)
          +"&remember="+encodeURIComponent(inputs.remember),
    type: "POST",
  success:function(res){
    console.log(res);
    let status = JSON.parse(res).status;

    if (status === "success") {
      location.reload();
    } else {
      let msg = JSON.parse(res).msg;
      $("#errormessage").text(msg); //error message
    }
  },
  error:function(err){
    let res = JSON.parse(err.responseText);

    $("#errormessage").text(res.msg); //error message
  }
  });
});

// update the variables
$(document).on('input', 'input', function() {
  let key = $(this).attr('name');
  let value = $(this).val();

  if (key) inputs[key] = value;
});

// toggle the remember variable
$(document).on('input', 'input[type=checkbox]', function() {
  $(this).is(':checked') ? inputs.remember = true : inputs.remember = false;
});

// switch between login and register
$(document).on('click', '#toggleState', function() {
  if ($(this).text() === "Register") {
    $(this).text("Log In");
    $("button[type=submit]").text("Register");
    $("#login-title").text("Register");
  } else {
    $(this).text("Register");
    $("button[type=submit]").text("Log In");
    $("#login-title").text("Log In");
  }
})

