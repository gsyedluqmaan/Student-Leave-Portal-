<?php
session_start();
include "PHP/navbar.php";

// Redirect to login page if session is not set
if (!isset($_SESSION['user_id']) || !isset($_SESSION['name']) || !isset($_SESSION['class']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

$backgroundImage = ($_SESSION['role'] == 'HOD' || $_SESSION['role'] == 'class_coordinator') ? 'mainbg.svg' : 'mainbg1.svg';
$textColor = ($_SESSION['role'] == 'HOD' || $_SESSION['role'] == 'class_coordinator') ? '#1e293b' : 'white';


// Quotes array
$quotes = [
    ["text" => "Be the change you wish to see in the world.", "author" => "Mahatma Gandhi"],
    ["text" => "The only way to do great work is to love what you do.", "author" => "Steve Jobs"],
    ["text" => "Everything you've ever wanted is on the other side of fear.", "author" => "George Addair"],
    ["text" => "Success is not final, failure is not fatal: it is the courage to continue that counts.", "author" => "Winston Churchill"],
    ["text" => "The future belongs to those who believe in the beauty of their dreams.", "author" => "Eleanor Roosevelt"],
    ["text" => "Don't watch the clock; do what it does. Keep going.", "author" => "Sam Levenson"],
    ["text" => "The only limit to our realization of tomorrow will be our doubts of today.", "author" => "Franklin D. Roosevelt"],
    ["text" => "Life is 10% what happens to you and 90% how you react to it.", "author" => "Charles R. Swindoll"]
];

// Get a random quote
$randomIndex = array_rand($quotes);
$currentQuote = $quotes[$randomIndex];

// Students array with their birthdays
$students = [
    ["name" => "Harish", "dob" => "2024-11-25", "class" => "A"],
];

// Leave Management Data
$on_leave_today = [
    ["name" => "Kavya", "class" => "A", "reason" => "Medical", "duration" => "1 day"],
    ["name" => "Gangaothri", "class" => "B", "reason" => "Family Event", "duration" => "2 days"],
    ["name" => "Harish", "class" => "A", "reason" => "Personal", "duration" => "1 day"]
];

$upcoming_leave = [
    ["name" => "Syed", "class" => "B", "reason" => "Medical Appointment"],
    ["name" => "Azhar", "class" => "A", "reason" => "Sports Event"]
];

// Set timezone and get today's date
date_default_timezone_set('Asia/Kolkata');
$today = date("Y-m-d");

// Filter students whose birthday matches today
$birthdays = array_filter($students, function ($student) use ($today) {
    return $student['dob'] === $today;
});

$total_classes = 45;
$conducted_classes = 24;
$absent_classes = 4;

// Calculate attendance percentage
$attended_classes = $conducted_classes - $absent_classes;
$attendance_percentage = ($attended_classes / $conducted_classes) * 100;

// Calculate required actions
if ($attendance_percentage > 75) {
    $classes_can_bunk = floor($attended_classes / 0.75) - $conducted_classes;
    $message = "You can bunk $classes_can_bunk more classes and maintain 75% attendance.";
} elseif ($attendance_percentage < 75) {
    $classes_to_attend = ceil((0.75 * $conducted_classes) - $attended_classes);
    $message = "You need to attend $classes_to_attend more classes to maintain 75% attendance.";
} else {
    $message = "Perfect! You have exactly 75% attendance.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Dashboard</title>
    <script src="https://cdn.fusioncharts.com/fusioncharts/latest/fusioncharts.js"></script>
    <script src="https://cdn.fusioncharts.com/fusioncharts/latest/fusioncharts.charts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.fusioncharts.com/fusioncharts/latest/fusioncharts.js"></script>
    <script src="https://cdn.fusioncharts.com/fusioncharts/latest/themes/fusioncharts.theme.fusion.js"></script>

    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <link rel="stylesheet" href="CSS/dash.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            /* background: linear-gradient(135deg, #ff7e5f, #feb47b); */
            background-image: url('<?php echo $backgroundImage; ?>');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-position: center;
            color: var(--text-primary);
            margin: 0;
            min-height: 100vh;
            line-height: 1.6;
        }

        h1 {
            font-size: 3rem;
            font-weight: 700;
            color:
                <?php echo $textColor; ?>
            ;
            text-align: center;
        }
    </style>
</head>

<body>
    <h1>Dashboard</h1>
    <div class="main-container">
        <div class="top">
            <div class="card quote-container">
                <div class="quote-text">
                    <?php echo $currentQuote['text']; ?>
                </div>
                <div class="quote-author">
                    <?php echo $currentQuote['author']; ?>
                </div>
            </div>

            <div class="card birthday-container">
                <h2>Today's Birthdays</h2>
                <?php if (!empty($birthdays)): ?>
                    <?php foreach ($birthdays as $birthday): ?>
                        <div class="birthday-card">
                            <h3><?php echo htmlspecialchars($birthday['name']); ?></h3>
                            <p>Class: <?php echo htmlspecialchars($birthday['class']); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No birthdays today!</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="middle">
            <!-- Current Leave Table -->
            <div class="card">
                <h2>Currently On Leave</h2>
                <div class="leave-table-container">
                    <table class="leave-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Class</th>
                                <th>Reason</th>
                                <th>Duration</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($on_leave_today as $leave): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($leave['name']); ?></td>
                                    <td><?php echo htmlspecialchars($leave['class']); ?></td>
                                    <td><?php echo htmlspecialchars($leave['reason']); ?></td>
                                    <td><?php echo htmlspecialchars($leave['duration']); ?></td>
                                    <td><span class="leave-status status-today">On Leave</span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Upcoming Leave Table -->
            <div class="card">
                <h2>Upcoming Leave</h2>
                <div class="leave-table-container">
                    <table class="leave-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Class</th>
                                <th>Reason</th>
                                <!-- <th>From</th>
                                <th>To</th> -->
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($upcoming_leave as $leave): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($leave['name']); ?></td>
                                    <td><?php echo htmlspecialchars($leave['class']); ?></td>
                                    <td><?php echo htmlspecialchars($leave['reason']); ?></td>
                                    <!-- <td><?php echo htmlspecialchars($leave['from']); ?></td>
                                    <td><?php echo htmlspecialchars($leave['to']); ?></td> -->
                                    <td><span class="leave-status status-upcoming">Upcoming</span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="bottom">
            <div class="chart-main-container">
                <div id="chart-container"></div>
            </div>

            <div class="chart-main-container-apex">
                <div id="bar-chart"></div>
            </div>
        </div>
    </div>

    <script>
        // Continuing the FusionCharts configuration
        const chartData = [
            {
                label: "Classes Attended",
                value: <?php echo json_encode($attended_classes); ?>,
                color: "#28a745"
            },
            {
                label: "Classes Absent",
                value: <?php echo json_encode($absent_classes); ?>,
                color: "#dc3545"
            }
        ];

        // Now configure and render the FusionChart
        FusionCharts.ready(function () {
            const chartConfig = {
                type: "pie3d",
                renderAt: "chart-container",
                width: "100%",
                height: "400",
                dataFormat: "json",
                dataSource: {
                    chart: {
                        caption: "Attendance Breakdown",
                        subCaption: "Total Classes: <?php echo $total_classes; ?>",
                        theme: "fusion",
                        showLabels: 1,
                        showLegend: 1,
                        enableSmartLabels: 1,
                        startingAngle: "0",
                        showPercentValues: 1,
                        decimals: 1,
                        useDataPlotColorForLabels: 1,
                        pieRadius: "45%",
                        numberSuffix: "",
                        bgColor: "#ffffff",
                        showBorder: 0,
                        borderAlpha: "0",
                        chartTopMargin: "0",
                        chartBottomMargin: "0",
                        chartLeftMargin: "0",
                        chartRightMargin: "0"
                    },
                    data: chartData
                }
            };

            // Create and render the chart
            const fusionChart = new FusionCharts(chartConfig);
            fusionChart.render();
        });

        // ApexCharts Configuration
        document.addEventListener('DOMContentLoaded', function () {
            const options = {
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: {
                        show: false
                    },
                    fontFamily: 'Inter, sans-serif',
                    foreColor: '#64748b'
                },
                series: [{
                    name: 'Attendance',
                    data: [<?php echo $attended_classes; ?>, <?php echo $absent_classes; ?>]
                }],
                plotOptions: {
                    bar: {
                        distributed: true,
                        borderRadius: 8,
                        horizontal: false,
                        columnWidth: '50%'
                    }
                },
                colors: ['#28a745', '#dc3545'],
                dataLabels: {
                    enabled: true,
                    style: {
                        fontSize: '14px',
                        fontWeight: 600,
                        fontFamily: 'Inter, sans-serif'
                    }
                },
                legend: {
                    show: false
                },
                xaxis: {
                    categories: ['Present', 'Absent'],
                    labels: {
                        style: {
                            fontSize: '14px',
                            fontWeight: 500,
                            fontFamily: 'Inter, sans-serif'
                        }
                    },
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            fontSize: '14px',
                            fontFamily: 'Inter, sans-serif'
                        }
                    }
                },
                grid: {
                    borderColor: '#e2e8f0',
                    strokeDashArray: 4,
                    xaxis: {
                        lines: {
                            show: false
                        }
                    }
                },
                title: {
                    text: 'Attendance Overview',
                    align: 'center',
                    style: {
                        fontSize: '18px',
                        fontWeight: 600,
                        fontFamily: 'Inter, sans-serif',
                        color: '#1e293b'
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val + " classes"
                        }
                    },
                    theme: 'dark',
                    style: {
                        fontSize: '14px',
                        fontFamily: 'Inter, sans-serif'
                    }
                }
            };

            // Create and render the ApexChart
            const apexChart = new ApexCharts(document.querySelector("#bar-chart"), options);
            apexChart.render();
        });

        // Tooltip functionality
        document.addEventListener('DOMContentLoaded', function () {
            const tooltip = document.createElement('div');
            tooltip.classList.add('tooltip');
            document.body.appendChild(tooltip);

            const cards = document.querySelectorAll('.card');
            cards.forEach(card => {
                card.addEventListener('mousemove', (e) => {
                    tooltip.style.left = e.pageX + 15 + 'px';
                    tooltip.style.top = e.pageY + 15 + 'px';
                });
            });
        });
    </script>
</body>

</html>