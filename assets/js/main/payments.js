$(document).ready(function () {
  $(".color-option").click(function () {
    $(".color-option").removeClass("selected");
    $(this).addClass("selected");
    $("#color_name").val($(this).attr("id"));
  });

  $(".paymentModel").click(function () {
    var userId = $(this).data("user_id");
    var userMonth = $(this).data("user_month");

    $.ajax({
      type: "POST",
      url: "../api/fetch_payment_data.php",
      data: {
        user_id: userId,
      },
      success: function (data) {
        var paymentData = JSON.parse(data);

        $("#user_id").val(userId);
        $("#payment_month").val(userMonth);
        $("#late_payment_fees").val(paymentData[0].late_fee);
        $("#total_payment_amount").val(paymentData[0].total_payment_amount);
        $("#PaymentsModal").modal("show");
      },
      error: function (xhr, status, error) {
        console.error(xhr.responseText);
      },
    });
  });

  $(".paymentTransaction").click(function () {
    var userId = $(this).data("user_id");
    var activeStatus = $(this).data("active");

    $.ajax({
      type: "POST",
      url: "../api/fetch_transactions_data.php",
      data: {
        user_id: userId,
        active: activeStatus,
      },
      success: function (data) {
        var transactions = JSON.parse(data);

        var listContainer = $("#yourListContainer");

        transactions.forEach(function (transaction) {
          var listItem = $("<li></li>").addClass(
            "list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg"
          );

          var firstDiv = $("<div></div>").addClass("d-flex align-items-center");
          var button = $("<button></button>").addClass(
            "btn btn-icon-only btn-rounded btn-outline-success mb-0 me-3 btn-sm d-flex align-items-center justify-content-center"
          );
          button.html('<i class="fas fa-arrow-up"></i>');
          var transactionDetails =
            $("<div></div>").addClass("d-flex flex-column");
          transactionDetails.append(
            '<h6 class="mb-1 text-dark text-sm">' +
              transaction.payment_month +
              "</h6>"
          );
          transactionDetails.append(
            '<span class="text-xs">' +
              transaction.payment_date +
              "     " +
              (transaction.additional_comments
                ? '<span style="color: red; font-weight: bold;">' +
                  transaction.additional_comments +
                  "</span>"
                : "") +
              "</span>"
          );

          firstDiv.append(button);
          firstDiv.append(transactionDetails);

          var secondDiv = $("<div></div>").addClass(
            "d-flex align-items-center text-success text-gradient text-sm font-weight-bold"
          );
          secondDiv.text("+ ₹ " + transaction.total_payment_amount);

          listItem.append(firstDiv);
          listItem.append(secondDiv);

          listContainer.append(listItem);
        });

        $("#PaymentTransactionModal").modal("show");
      },
      error: function (xhr, status, error) {
        console.error(xhr.responseText);
      },
    });
  });

  $("#PaymentTransactionModal").on("hidden.bs.modal", function () {
    $("#yourListContainer").empty();
  });
});

$(document).ready(function () {
  getFilterData();
  $("#monthSelect, #nameSearch, #numberSearch").on("input change", function () {
    $("#processing").show();
    $("#payments-table").hide();

    setTimeout(() => {
      getFilterData();
    }, 1000);
  });
});

function getFilterData() {
  $("#processing").hide();
  var selectedMonth = $("#monthSelect").val();
  var selectedPaymentType = "pending";
  var nameSearchText = $("#nameSearch").val().toLowerCase();
  var numberSearchText = $("#numberSearch").val().toLowerCase();

  $("tbody tr").each(function () {
    var showRow = true;

    var nameValue = $(this).find("td:eq(0) .text-sm").text().toLowerCase();
    var dueDateFire = $(this).find("td:eq(16) .duehit").text();
    var numberValue = $(this).find("td:eq(1) .text-sm").text().toLowerCase();

    if (
      selectedMonth !== "" &&
      selectedMonth !== null &&
      selectedPaymentType !== "" &&
      selectedPaymentType !== null
    ) {
      var availability = $(this)
        .find("td:eq(" + selectedMonth + ") .badge-sm")
        .text()
        .toLowerCase();
      showRow =
        showRow &&
        availability.includes(selectedPaymentType) &&
        dueDateFire.includes("true");
    }

    showRow =
      showRow &&
      nameValue.includes(nameSearchText) &&
      numberValue.includes(numberSearchText);
    $(this).toggle(showRow);
  });

  $("#payments-table").show();
}
