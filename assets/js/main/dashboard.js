var chartType = "line";
var chartInstance = null;

function createChart(data) {
  var ctx = document.getElementById("chart-line").getContext("2d");

  if (!data || Object.values(data).length === 0) {
    ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
    ctx.font = "18px Arial";
    ctx.fillStyle = "#000";
    ctx.fillText("Access Denied", 50, 50);
    return;
  }

  if (chartInstance) {
    chartInstance.destroy();
  }

  var values = Object.values(data);

  var chartConfig = {
    type: chartType,
    data: {
      labels: [
        "Jan",
        "Feb",
        "Mar",
        "Apr",
        "May",
        "Jun",
        "Jul",
        "Aug",
        "Sep",
        "Oct",
        "Nov",
        "Dec",
      ],
      datasets: [
        {
          label: "Profits",
          tension: 0.4,
          borderWidth: 3,
          pointRadius: 0,
          borderColor: "#4e73df",
          backgroundColor: "rgba(78, 115, 223, 0.6)",
          fill: true,
          data: values,
          maxBarThickness: 6,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false,
        },
      },
      interaction: {
        intersect: false,
        mode: "index",
      },
      scales: {
        y: {
          grid: {
            drawBorder: false,
            display: true,
            drawOnChartArea: true,
            drawTicks: false,
            borderDash: [5, 5],
          },
          ticks: {
            display: true,
            padding: 10,
            color: "#fbfbfb",
            font: {
              size: 11,
              family: "Open Sans",
              style: "normal",
              lineHeight: 2,
            },
          },
          title: {
            display: true,
            text: "Profits (in ₹)",
            color: "#4e73df",
            font: {
              size: 14,
              weight: "bold",
            },
          },
        },
        x: {
          grid: {
            drawBorder: false,
            display: false,
            drawOnChartArea: false,
            drawTicks: false,
            borderDash: [5, 5],
          },
          ticks: {
            display: true,
            color: "#ccc",
            padding: 20,
            font: {
              size: 11,
              family: "Open Sans",
              style: "normal",
              lineHeight: 2,
            },
          },
          title: {
            display: false,
            text: "Months",
            color: "#4e73df",
            font: {
              size: 14,
              weight: "bold",
            },
          },
        },
      },
      animation: {
        duration: 1000,
        easing: "easeInOutQuad",
      },
    },
  };

  chartInstance = new Chart(ctx, chartConfig);
}

function fetchData() {
  $.ajax({
    url: "../api/fetch_net_profit.php",
    method: "POST",
    dataType: "json",
    success: function (data) {
      createChart(data.monthly_profits);
    },
    error: function (error) {
      console.error("Error fetching data:", error);
      createChart(null);
    },
  });
}

$("#toggleChartType").on("click", function () {
  chartType = chartType === "bar" ? "line" : "bar";
  fetchData();
});

fetchData();

var win = navigator.platform.indexOf("Win") > -1;
if (win && document.querySelector("#sidenav-scrollbar")) {
  var options = {
    damping: "0.5",
  };
  Scrollbar.init(document.querySelector("#sidenav-scrollbar"), options);
}
