/**
 * buttons with the id logout will become a logout button for the login-lib
 */
$(document).on('click', "#logout", function() {
  jQuery.ajax({
    url: "ajax/login/logout.php",
    type: "POST",
    success:function() {
      location.reload();
    },
    error:function(err) {
      console.error(err);
    }
  });
});