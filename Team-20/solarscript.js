/* =========================================================
   SOLARIS SOLAR SAVINGS CALCULATOR
========================================================= */


let savingsChart = null;


/* =========================================================
   FORMATTERS
========================================================= */


function formatNumber(value) {

    return new Intl.NumberFormat("en-IN", {

        maximumFractionDigits: 0

    }).format(value);

}


function formatCurrency(value) {

    return new Intl.NumberFormat("en-IN", {

        style: "currency",

        currency: "INR",

        maximumFractionDigits: 0

    }).format(value);

}


function formatLakhs(value) {

    if (value >= 10000000) {

        return "₹" +
            (value / 10000000).toFixed(1) +
            "Cr";

    }


    if (value >= 100000) {

        return "₹" +
            (value / 100000).toFixed(1) +
            "L";

    }


    if (value >= 1000) {

        return "₹" +
            (value / 1000).toFixed(1) +
            "K";

    }


    return formatCurrency(value);

}


/* =========================================================
   CALCULATOR
========================================================= */


function calculateSavings() {


    /* -----------------------------------------------------
       READ INPUTS
    ----------------------------------------------------- */


    const monthlyUsage =
        Number(
            document.getElementById(
                "monthlyUsage"
            ).value
        );


    const tariff =
        Number(
            document.getElementById(
                "tariff"
            ).value
        );


    const systemSize =
        Number(
            document.getElementById(
                "systemSize"
            ).value
        );


    const installationCost =
        Number(
            document.getElementById(
                "installationCost"
            ).value
        );


    const subsidy =
        Number(
            document.getElementById(
                "subsidy"
            ).value
        );


    const generationFactor =
        Number(
            document.getElementById(
                "generationFactor"
            ).value
        );


    /* -----------------------------------------------------
       VALIDATION
    ----------------------------------------------------- */


    if (

        monthlyUsage <= 0 ||

        tariff <= 0 ||

        systemSize <= 0 ||

        installationCost < 0 ||

        subsidy < 0 ||

        generationFactor <= 0

    ) {

        alert(
            "Please enter valid values in all required fields."
        );

        return;

    }


    /* -----------------------------------------------------
       ANNUAL ELECTRICITY CONSUMPTION
    ----------------------------------------------------- */


    const annualConsumption =
        monthlyUsage * 12;


    /* -----------------------------------------------------
       ANNUAL SOLAR GENERATION

       System Size × Daily Yield × 365
    ----------------------------------------------------- */


    const annualGeneration =
        systemSize *
        generationFactor *
        365;


    /* -----------------------------------------------------
       USABLE SOLAR

       In this simplified model, electricity savings
       cannot exceed annual electricity consumption.
    ----------------------------------------------------- */


    const usableSolar =
        Math.min(
            annualGeneration,
            annualConsumption
        );


    /* -----------------------------------------------------
       ANNUAL SAVINGS
    ----------------------------------------------------- */


    const annualSavings =
        usableSolar * tariff;


    /* -----------------------------------------------------
       ENERGY OFFSET
    ----------------------------------------------------- */


    const energyOffset =
        Math.min(

            (
                annualGeneration /
                annualConsumption
            ) * 100,

            100

        );


    /* -----------------------------------------------------
       NET INVESTMENT
    ----------------------------------------------------- */


    const netInvestment =
        Math.max(

            installationCost -
            subsidy,

            0

        );


    /* -----------------------------------------------------
       PAYBACK
    ----------------------------------------------------- */


    let payback = 0;


    if (annualSavings > 0) {

        payback =
            netInvestment /
            annualSavings;

    }


    /* -----------------------------------------------------
       CO2

       Example model factor:
       0.82 kg CO2 / kWh

       Make this configurable for production use.
    ----------------------------------------------------- */


    const co2Factor = 0.82;


    const annualCO2 =
        annualGeneration *
        co2Factor /
        1000;


    /* -----------------------------------------------------
       25 YEAR MODEL
    ----------------------------------------------------- */


    const years = [];

    const cumulativeSavings = [];

    const yearlySavings = [];

    const yearlyGeneration = [];


    let cumulative = 0;


    let currentAnnualSavings =
        annualSavings;


    let currentGeneration =
        annualGeneration;


    for (
        let year = 1;
        year <= 25;
        year++
    ) {

        years.push(
            "Year " + year
        );


        /*
            Assume 0.5% annual solar degradation.
        */

        if (year > 1) {

            currentGeneration *= 0.995;

        }


        /*
            Assume electricity prices rise
            by 5% per year.

            This is a simplified assumption.
        */

        if (year > 1) {

            currentAnnualSavings *= 1.05;

            currentAnnualSavings *= 0.995;

        }


        cumulative +=
            currentAnnualSavings;


        yearlySavings.push(
            currentAnnualSavings
        );


        yearlyGeneration.push(
            currentGeneration
        );


        cumulativeSavings.push(
            Math.max(
                cumulative -
                netInvestment,

                0
            )
        );

    }


    const lifetimeGeneration =
        yearlyGeneration.reduce(
            (total, value) =>
                total + value,
            0
        );


    const lifetimeGrossSavings =
        yearlySavings.reduce(
            (total, value) =>
                total + value,
            0
        );


    const lifetimeNetSavings =
        Math.max(
            lifetimeGrossSavings -
            netInvestment,

            0
        );


    const lifetimeCO2 =
        lifetimeGeneration *
        co2Factor /
        1000;


    /* =====================================================
       UPDATE UI
    ===================================================== */


    document.getElementById(
        "annualGeneration"
    ).textContent =
        formatNumber(
            annualGeneration
        );


    document.getElementById(
        "annualSavings"
    ).textContent =
        formatCurrency(
            annualSavings
        );


    document.getElementById(
        "energyOffset"
    ).textContent =
        energyOffset.toFixed(1) +
        "%";


    document.getElementById(
        "paybackPeriod"
    ).textContent =
        payback.toFixed(1) +
        " yrs";


    document.getElementById(
        "co2Savings"
    ).textContent =
        annualCO2.toFixed(1) +
        " t";


    document.getElementById(
        "summaryCost"
    ).textContent =
        formatCurrency(
            installationCost
        );


    document.getElementById(
        "summarySubsidy"
    ).textContent =
        "− " +
        formatCurrency(
            subsidy
        );


    document.getElementById(
        "netInvestment"
    ).textContent =
        formatCurrency(
            netInvestment
        );


    /* =====================================================
       HERO
    ===================================================== */


    document.getElementById(
        "heroGeneration"
    ).textContent =
        formatNumber(
            annualGeneration
        ) +
        " kWh";


    document.getElementById(
        "heroSavings"
    ).textContent =
        formatCurrency(
            annualSavings
        );


    /* =====================================================
       LONG-TERM NUMBERS
    ===================================================== */


    document.getElementById(
        "lifetimeGeneration"
    ).textContent =
        formatNumber(
            lifetimeGeneration
        );


    document.getElementById(
        "lifetimeSavings"
    ).textContent =
        formatLakhs(
            lifetimeNetSavings
        );


    document.getElementById(
        "lifetimeCO2"
    ).textContent =
        lifetimeCO2.toFixed(1) +
        " t";


    document.getElementById(
        "systemCapacityDisplay"
    ).textContent =
        systemSize +
        " kW";


    document.getElementById(
        "impactCO2"
    ).textContent =
        lifetimeCO2.toFixed(1) +
        " tonnes";


    document.getElementById(
        "chartFinalValue"
    ).textContent =
        formatLakhs(
            lifetimeNetSavings
        );


    /* =====================================================
       UPDATE CHART
    ===================================================== */


    createChart(
        years,
        cumulativeSavings
    );

}


/* =========================================================
   CHART
========================================================= */


function createChart(
    years,
    savings
) {


    const canvas =
        document.getElementById(
            "savingsChart"
        );


    if (!canvas) {

        return;

    }


    const ctx =
        canvas.getContext(
            "2d"
        );


    if (savingsChart) {

        savingsChart.destroy();

    }


    /*
        Gradient for chart area
    */

    const gradient =
        ctx.createLinearGradient(
            0,
            0,
            0,
            400
        );


    gradient.addColorStop(
        0,
        "rgba(245, 158, 11, 0.25)"
    );


    gradient.addColorStop(
        1,
        "rgba(245, 158, 11, 0.01)"
    );


    savingsChart =
        new Chart(

            ctx,

            {

                type: "line",

                data: {

                    labels: years,

                    datasets: [

                        {

                            label:
                                "Cumulative net savings",

                            data:
                                savings,

                            borderColor:
                                "#f59e0b",

                            backgroundColor:
                                gradient,

                            borderWidth:
                                3,

                            fill:
                                true,

                            tension:
                                0.38,

                            pointRadius:
                                0,

                            pointHoverRadius:
                                5,

                            pointHoverBackgroundColor:
                                "#f59e0b",

                            pointHoverBorderColor:
                                "#ffffff",

                            pointHoverBorderWidth:
                                2

                        }

                    ]

                },


                options: {

                    responsive:
                        true,

                    maintainAspectRatio:
                        false,


                    interaction: {

                        intersect:
                            false,

                        mode:
                            "index"

                    },


                    plugins: {

                        legend: {

                            display:
                                false

                        },


                        tooltip: {

                            backgroundColor:
                                "#0b1726",

                            titleColor:
                                "#ffffff",

                            bodyColor:
                                "#ffffff",

                            padding:
                                12,

                            displayColors:
                                false,

                            callbacks: {

                                label:
                                    function(context) {

                                        return (
                                            "Savings: " +
                                            formatCurrency(
                                                context.raw
                                            )
                                        );

                                    }

                            }

                        }

                    },


                    scales: {

                        x: {

                            grid: {

                                display:
                                    false

                            },

                            ticks: {

                                color:
                                    "#8994a2",

                                font: {

                                    size:
                                        10

                                },

                                maxTicksLimit:
                                    9

                            }

                        },


                        y: {

                            beginAtZero:
                                true,

                            grid: {

                                color:
                                    "rgba(11,23,38,0.06)"

                            },

                            ticks: {

                                color:
                                    "#8994a2",

                                font: {

                                    size:
                                        10

                                },

                                callback:
                                    function(value) {

                                        if (
                                            value >=
                                            100000
                                        ) {

                                            return (
                                                "₹" +
                                                (
                                                    value /
                                                    100000
                                                ).toFixed(1) +
                                                "L"
                                            );

                                        }


                                        if (
                                            value >=
                                            1000
                                        ) {

                                            return (
                                                "₹" +
                                                (
                                                    value /
                                                    1000
                                                ).toFixed(0) +
                                                "K"
                                            );

                                        }


                                        return "₹" + value;

                                    }

                            }

                        }

                    }

                }

            }

        );

}


/* =========================================================
   INPUT AUTO CALCULATION
========================================================= */


function setupAutoCalculation() {

    const inputs =
        document.querySelectorAll(
            "input"
        );


    inputs.forEach(
        function(input) {

            input.addEventListener(
                "input",
                function() {

                    calculateSavings();

                }
            );

        }
    );

}


/* =========================================================
   PAGE LOAD
========================================================= */


document.addEventListener(
    "DOMContentLoaded",
    function() {

        calculateSavings();

        setupAutoCalculation();

    }
);