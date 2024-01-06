function createForm(url, method, params) {
  const form = document.createElement("form");
  form.method = method;
  form.action = url;

  for (const key in params) {
    if (params.hasOwnProperty(key)) {
      const input = document.createElement("input");
      input.type = "hidden";
      input.name = key;
      input.value = params[key];
      form.appendChild(input);
    }
  }

  document.body.appendChild(form);
  form.submit();
}

function confirmDelete(roomId) {
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
      createForm("rooms.php", "POST", {
        room_id: roomId,
      });
    }
  });
}

$(document).ready(function () {
  $("#addRoom").click(function () {
    $("#room_id").val("");
    $("#room_type").val("");
    $("#room_category").val("");
    $("#room_number").val("");
    $("#room_capacity").val("");

    $("#RoomModal").modal("show");
  });

  $(".updateRoom").click(function () {
    var room_id = $(this).data("room_id");
    var room_type = $(this).data("room_type");
    var room_category = $(this).data("room_category");
    var room_number = $(this).data("room_number");
    var room_capacity = $(this).data("room_capacity");

    $("#room_id").val(room_id);
    $("#room_type").val(room_type);
    $("#room_category").val(room_category);
    $("#room_number").val(room_number);
    $("#room_capacity").val(room_capacity);

    $("#RoomModal").modal("show");
  });

  $(".roomUser").click(function () {
    var roomId = $(this).data("room_id");
    $.ajax({
      type: "POST",
      url: "../api/fetch_boarders_data.php",
      data: {
        roomId: roomId,
      },
      success: function (data) {
        var boarders = JSON.parse(data);

        var currentListContainer = $("#currentListContainer");
        var leftListContainer = $("#leftListContainer");

        boarders.forEach(function (boarder) {
          var listItem = $("<li></li>").addClass(
            "list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg"
          );

          var firstDiv = $("<div></div>").addClass("d-flex align-items-center");

          var boarderName = $("<div></div>").addClass("d-flex flex-column");
          
          boarderName.append(
            "<h6 class=\"mb-1 text-dark text-sm\" onclick=\"createForm('view-boarders.php', 'POST', {'user_id':" +
              boarder.user_id +
              '})">' +
              boarder.name +
              "</h6>"
          );

          boarderName.append(
            "<span class=\"text-xs\" onclick=\"createForm('view-boarders.php', 'POST', {'user_id':" +
              boarder.user_id +
              '})">' +
              boarder.number +
              "</span>"
          );

          firstDiv.append(boarderName);

          listItem.append(firstDiv);
          if (boarder.active == 1) {
            currentListContainer.append(listItem);
          } else {
            leftListContainer.append(listItem);
          }
        });

        $("#RoomUserModal").modal("show");
      },
      error: function (xhr) {
        console.error(xhr.responseText);
      },
    });
  });

  $("#RoomUserModal").on("hidden.bs.modal", function () {
    $("#currentListContainer").empty();
    $("#leftListContainer").empty();
  });
});

$(document).ready(function () {
  $(
    "#roomTypeSelect, #categorySearch, #roomNumberSearch, #availabilitySelect"
  ).on("input change", function () {
    var roomType = $("#roomTypeSelect").val();
    var categorySearch = $("#categorySearch").val().toLowerCase();
    var roomNumberSearch = $("#roomNumberSearch").val().toLowerCase();
    var availabilitySelect = $("#availabilitySelect").val();

    $("tbody tr").each(function () {
      $("tbody tr").each(function () {
        var showRow = true;

        var roomTypeValue = $(this).find("td:eq(0) span").text().toLowerCase();
        var category = $(this).find("td:eq(1) span").text().toLowerCase();
        var roomNumber = $(this).find("td:eq(2) span").text().toLowerCase();
        var availability = $(this).find("td:eq(3) span").text().toLowerCase();

        showRow =
          showRow &&
          category.includes(categorySearch) &&
          roomNumber.includes(roomNumberSearch);

        if (roomType !== "" && roomType !== null) {
          showRow = showRow && roomTypeValue.includes(roomType);
        }

        if (availabilitySelect !== "" && availabilitySelect !== null) {
          showRow = showRow && availability.includes(availabilitySelect);
        }

        $(this).toggle(showRow);
      });
    });
  });
});
