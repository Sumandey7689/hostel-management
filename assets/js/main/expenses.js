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

function confirmDelete(expenses_id) {
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
      createForm("expenses.php", "POST", {
        expenses_id: expenses_id,
      });
    }
  });
}

$(document).ready(function () {
  $("#addItem").click(function () {
    $("#expenses_id").val("");
    $("#item").val("");
    $("#amount").val("");

    $("#addItemModal").modal("show");
  });

  $(".expensesItem").click(function () {
    var expensesId = $(this).data("expenses_id");

    $.ajax({
      type: "POST",
      url: "../api/fetch_expenses_data.php",
      data: {
        expenses_id: expensesId,
      },
      success: function (data) {
        var expenses = JSON.parse(data);
        $("#expenses_id").val(expensesId);

        $("#item").val(expenses[0].item);
        $("#amount").val(expenses[0].amount);

        $("#addItemModal").modal("show");
      },
      error: function (xhr) {
        console.error(xhr.responseText);
      },
    });
  });
});

$(document).ready(function () {
  $("#monthSelect, #yearSelect").on("input change", function () {
    var monthSelect = $("#monthSelect").val();
    var yearSelect = $("#yearSelect").val();

    $("tbody tr").each(function () {
      var showRow = true;

      var month = $(this).find("td:eq(3) span").text().toLowerCase();
      var year = $(this).find("td:eq(3) span").text();

      if (monthSelect !== "" && monthSelect !== null) {
        showRow = showRow && month.includes(monthSelect);
      }

      if (yearSelect !== "" && yearSelect !== null) {
        showRow = showRow && year.includes(yearSelect);
      }

      $(this).toggle(showRow);
    });
  });
});
