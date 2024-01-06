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
      createForm("boarders.php", "POST", {
        user_id: userId,
      });
    }
  });
}

$(document).ready(function () {
  $("#addUser").click(function () {
    // Load Payments Data
    $("#add-payment_due_date").val(
      new Date(new Date().setMonth(new Date().getMonth() + 1))
        .toISOString()
        .slice(0, 10)
    );
    $("#add-payment_date").val(new Date().toISOString().slice(0, 10));

    // Load Rooms Data
    $.ajax({
      type: "POST",
      url: "../api/fetch_room_type.php",
      success: function (roomType) {
        var roomTypeData = JSON.parse(roomType);

        populateRoomTypeDropdown(roomTypeData, "#add-room_type_dropdown");
        $("#AddUserModal").modal("show");

        $("#add-room_type_dropdown").change(function () {
          var selectedRoomType = $(this).val();
          fetchRoomInformation(
            "userId",
            selectedRoomType,
            "#AddUserModal",
            "#add-room_category_dropdown",
            "#add-room_number_dropdown"
          );
        });
      },
      error: function (xhr) {
        console.error(xhr.responseText);
      },
    });

    $("#AddUserModal").modal("show");
  });

  $(".updateUser").click(function () {
    // Get user data from the row
    var user_id = $(this).data("user_id");
    var name = $(this).data("name");
    var number = $(this).data("number");
    var location_type = $(this).data("location_type");
    var subject = $(this).data("subject");
    var year = $(this).data("year");
    var semester = $(this).data("semester");
    var organizationname = $(this).data("organizationname");

    // Set form values
    $("#user_user_id").val(user_id);
    $("#name").val(name);
    $("#number").val(number);
    $("#location_type").val(location_type);
    $("#subject").val(subject);
    $("#year").val(year);
    $("#semester").val(semester);
    $("#organizationname").val(organizationname);

    $("#UpdateUserModal").modal("show");
  });

  // Address Status
  $(".AddressStatus").click(function () {
    var userId = $(this).data("user_id");

    $("#address_user_id").val(userId);
    $("#street_address").val("");
    $("#city").val("");
    $("#state").val("");
    $("#postal_code").val("");
    $("#AddressModal").modal("show");
  });

  $(".addressUpdateStatus").click(function () {
    var userId = $(this).data("user_id");

    $.ajax({
      type: "POST",
      url: "../api/fetch_address_data.php",
      data: {
        user_id: userId,
      },
      success: function (data) {
        var addressData = JSON.parse(data);

        $("#address_user_id").val(userId);
        $("#street_address").val(addressData[0].street_address);
        $("#city").val(addressData[0].city);
        $("#state").val(addressData[0].state);
        $("#postal_code").val(addressData[0].postal_code);
        $("#AddressModal").modal("show");
      },
      error: function (xhr, status, error) {
        console.error(xhr.responseText);
      },
    });
  });

  // Payments Status
  $(".paymetsStatus").click(function () {
    var userId = $(this).data("user_id");

    $("#payments_user_id").val(userId);
    $("#payment_due_date").val(
      new Date(new Date().setMonth(new Date().getMonth() + 1))
        .toISOString()
        .slice(0, 10)
    );
    $("#payment_date").val(new Date().toISOString().slice(0, 10));
    $("#total_payment_amount").val("");

    function addAdditionalSection() {
      var additionalSection = $(
        '<div class="form-group pt-2" id="additional_comments"></div>'
      );
      var label = $(
        '<label for="additional_comments">Additional Comments:</label>'
      );
      var textArea = $(
        '<textarea class="form-control" name="additional_comments" placeholder="Enter additional comments"></textarea>'
      );

      additionalSection.append(label, textArea);
      $("#total_payment_amount").after(additionalSection);
    }
    addAdditionalSection();

    $("#PaymentsModal").modal("show");
  });

  $("#PaymentsModal").on("hidden.bs.modal", function () {
    $('textarea[name="additional_comments"]').remove();
    $('label[for="additional_comments"]').remove();
    $('div[id="additional_comments"]').remove();
  });

  $(".paymentUpdateStatus").click(function () {
    var userId = $(this).data("user_id");

    $.ajax({
      type: "POST",
      url: "../api/fetch_payment_data.php",
      data: {
        user_id: userId,
      },
      success: function (data) {
        var paymentData = JSON.parse(data);

        $("#payments_user_id").val(userId);
        $("#payment_date").val(paymentData[0].payment_date);
        $("#payment_due_date").val(paymentData[0].payment_due_date);
        $("#grace_period").val(paymentData[0].grace_period);
        $("#total_payment_amount").val(paymentData[0].total_payment_amount);
        $("#PaymentsModal").modal("show");
      },
      error: function (xhr, status, error) {
        console.error(xhr.responseText);
      },
    });
  });

  // Room Add Status
  $(".roomStatus").click(function () {
    var userId = $(this).data("user_id");
    $.ajax({
      type: "POST",
      url: "../api/fetch_room_type.php",
      success: function (roomType) {
        var roomTypeData = JSON.parse(roomType);

        populateRoomTypeDropdown(roomTypeData, "#room_type_dropdown");
        $("#RoomModal").modal("show");

        $("#room_type_dropdown").change(function () {
          var selectedRoomType = $(this).val();
          fetchRoomInformation(
            userId,
            selectedRoomType,
            "#RoomModal",
            "#room_category_dropdown",
            "#room_number_dropdown"
          );
        });
      },
      error: function (xhr) {
        console.error(xhr.responseText);
      },
    });

    $("#RoomModal").on("hidden.bs.modal", function () {
      $("#room_type_dropdown").empty();
      $("#room_category_dropdown").empty();
      $("#room_number_dropdown").empty();
    });
  });

  function populateRoomTypeDropdown(roomData, room_type_dropdown) {
    var dropdown = $(room_type_dropdown);
    dropdown.empty();
    dropdown.append(
      '<option value="" selected disabled>Select Room Category</option>'
    );

    for (var i = 0; i < roomData.length; i++) {
      dropdown.append(
        '<option value="' +
          roomData[i].room_type +
          '">' +
          roomData[i].room_type +
          "</option>"
      );
    }
  }

  function populateRoomCategoryDropdown(roomData, room_category_dropdown) {
    console.log(room_category_dropdown);
    var dropdown = $(room_category_dropdown);
    dropdown.empty();
    dropdown.append(
      '<option value="" selected disabled>Select Room Category</option>'
    );

    for (var i = 0; i < roomData.length; i++) {
      dropdown.append(
        '<option value="' +
          roomData[i].room_category +
          '">' +
          roomData[i].room_category +
          "</option>"
      );
    }
  }

  function populateRoomNumbersDropdown(roomData, room_number_dropdown) {
    var dropdown = $(room_number_dropdown);
    dropdown.empty();
    dropdown.append(
      '<option value="" selected disabled>Select Room Numbers</option>'
    );

    for (var i = 0; i < roomData.length; i++) {
      dropdown.append(
        '<option value="' +
          roomData[i].room_number +
          '">' +
          roomData[i].room_number +
          "</option>"
      );
    }
  }

  function fetchRoomInformation(
    userId,
    selectedRoomType,
    modelName,
    room_category_dropdown,
    room_number_dropdown
  ) {
    $.ajax({
      type: "POST",
      url: "../api/fetch_available_room_data.php",
      data: {
        room_type: selectedRoomType,
      },
      success: function (data) {
        var roomData = JSON.parse(data);

        if (Array.isArray(roomData) && roomData.length > 0) {
          $("#room_user_id").val(userId);

          populateRoomCategoryDropdown(roomData, room_category_dropdown);

          $(modelName).modal("show");

          $(room_category_dropdown).change(function () {
            var selectedRoomCategory = $(this).val();

            $.ajax({
              type: "POST",
              url: "../api/fetch_room_numbers.php",
              data: {
                room_type: selectedRoomType,
                room_category: selectedRoomCategory,
              },
              success: function (roomNumbers) {
                var roomNumbersData = JSON.parse(roomNumbers);
                populateRoomNumbersDropdown(
                  roomNumbersData,
                  room_number_dropdown
                );
              },
              error: function (xhr) {
                console.error(xhr.responseText);
              },
            });
          });
        } else {
          console.error("Invalid roomData structure:", roomData);
        }
      },
      error: function (xhr) {
        console.error(xhr.responseText);
      },
    });
  }

  // Room Update STatus
  $(".roomUpdateStatus").click(function () {
    var userId = $(this).data("user_id");

    $.ajax({
      type: "POST",
      url: "../api/fetch_room_data.php",
      data: {
        user_id: userId,
      },
      success: function (data) {
        var roomData = JSON.parse(data);

        var roomTypeDropdown = $("#room_type_dropdown");
        roomTypeDropdown.empty();

        if (roomData[0].room_type === "New") {
          roomTypeDropdown.append(
            '<option value="New" selected>New</option>' +
              '<option value="Old">Old</option>'
          );
        } else if (roomData[0].room_type === "Old") {
          roomTypeDropdown.append(
            '<option value="New">New</option>' +
              '<option value="Old" selected>Old</option>'
          );
        }

        var roomCategoryDropdown = $("#room_category_dropdown");
        roomCategoryDropdown.empty();
        roomCategoryDropdown.append(
          '<option value="' +
            roomData[0].room_category +
            '">' +
            roomData[0].room_category +
            "</option>"
        );

        var roomNumberDropdown = $("#room_number_dropdown");
        roomNumberDropdown.empty();
        roomNumberDropdown.append(
          '<option value="' +
            roomData[0].room_number +
            '">' +
            roomData[0].room_number +
            "</option>"
        );

        $("#RoomModal").modal("show");

        $("#room_type_dropdown").change(function () {
          $("#room_category_dropdown").empty();
          $("#room_number_dropdown").empty();

          var selectedRoomType = $(this).val();
          fetchRoomInformation(
            userId,
            selectedRoomType,
            "#RoomModal",
            "#room_category_dropdown",
            "#room_number_dropdown"
          );
        });
      },
      error: function (xhr) {
        console.error(xhr.responseText);
      },
    });
    $("#RoomModal").on("hidden.bs.modal", function () {
      $("#room_type_dropdown").empty();
      $("#room_category_dropdown").empty();
      $("#room_number_dropdown").empty();
    });
  });
});

$(".trackStatus").click(function () {
  var userId = $(this).data("user_id");

  $.ajax({
    type: "POST",
    url: "../api/fetch_room_tracking_data.php",
    data: {
      userId: userId,
    },
    success: function (data) {
      var rooms = JSON.parse(data);

      var roomListContainer = $("#roomListContainer");

      rooms.forEach(function (room) {
        var listItem = $("<li></li>").addClass(
          "list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg"
        );

        var firstDiv = $("<div></div>").addClass("d-flex align-items-center");

        var room_category = $("<div></div>").addClass("d-flex flex-column");

        room_category.append(
          '<div class="room-info">' +
            '<h6 class="mb-1 text-dark text-sm">' +
            room.room_type +
            '<span class="room-details">' +
            '<span class="room-category">' +
            room.room_category +
            "</span>" +
            '<span class="room-number">Room ' +
            room.room_number +
            "</span>" +
            "</span>" +
            "</h6>" +
            "</div>"
        );

        firstDiv.append(room_category);

        listItem.append(firstDiv);
        roomListContainer.append(listItem);
      });

      $("#RoomTrackingUserModal").modal("show");
    },
    error: function (xhr) {
      console.error(xhr.responseText);
    },
  });

  $("#RoomTrackingUserModal").on("hidden.bs.modal", function () {
    $("#roomListContainer").empty();
  });
});

$(document).ready(function () {
  $("#nameSearch, #roomSearch, #collegeOfficeSelect, #organizationSearch").on(
    "input change",
    function () {
      var nameSearch = $("#nameSearch").val().toLowerCase();
      var roomSearch = $("#roomSearch").val().toLowerCase();
      roomSearch = roomSearch.replace(/\s/g, "").replace(/[()]/g, "");

      var collegeOfficeSelect = $("#collegeOfficeSelect").val();

      $("tbody tr").each(function () {
        var name = $(this).find("td:eq(0) h6").text().toLowerCase();
        var roomInfo = $(this).find("td:eq(3) span").text().toLowerCase();
        roomInfo = roomInfo.replace(/\s/g, "").replace(/[()]/g, "");

        var locationType = $(this).find("td:eq(2) span").text().toLowerCase();

        var showRow =
          name.includes(nameSearch) && roomInfo.includes(roomSearch);

        if (collegeOfficeSelect && collegeOfficeSelect !== "null") {
          showRow = showRow && locationType.includes(collegeOfficeSelect);
        }

        $(this).toggle(showRow);
      });
    }
  );
});
