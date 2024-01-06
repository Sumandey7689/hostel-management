function confirmDelete(userId) {
  Swal.fire({
    title: "Are you sure?",
    text: "You won't be able to revert this!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, delete it!",
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = "master-user.php?id=" + userId;
    }
  });
}

function postData(userId, userStatus) {
  // Create a form dynamically
  var form = document.createElement("form");
  form.method = "post";
  form.action = "master-user.php";

  // Create input fields for the data
  var idInput = document.createElement("input");
  idInput.type = "hidden";
  idInput.name = "id";
  idInput.value = userId;

  var statusInput = document.createElement("input");
  statusInput.type = "hidden";
  statusInput.name = "status";
  statusInput.value = userStatus;

  // Append the input fields to the form
  form.appendChild(idInput);
  form.appendChild(statusInput);

  // Append the form to the document and submit it
  document.body.appendChild(form);
  form.submit();
}

$(document).ready(function () {
  $("#addUser").click(function () {
    $("#userId").val("");
    $("#name").val("");
    $("#username").val("");
    $("#password").val("");

    $("#UserModal").modal("show");
  });

  $(".updateUser").click(function () {
    // Get user data from the row
    var userId = $(this).data("userid");
    var name = $(this).data("name");
    var username = $(this).data("username");

    $("#userId").val(userId);
    $("#name").val(name);
    $("#username").val(username);

    $("#UserModal").modal("show");
  });
});
