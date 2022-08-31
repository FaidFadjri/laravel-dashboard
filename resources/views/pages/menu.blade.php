@extends('app')
@section('content')
    {{-- Hidden Input --}}
    <input type="hidden" class="form-control" id="js_baik" readonly>
    <input type="hidden" class="form-control" id="js_kurang" readonly>
    <input type="hidden" class="form-control" id="js_perlu" readonly>
    <input type="hidden" class="form-control" id="js_na" readonly>

    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">ELVIS Premises Monitoring Application</h3>
                <div class="nk-block-des text-soft">
                    <p>Dashboard by Service Quality Division</p>
                </div>
            </div>
        </div><!-- .nk-block-between -->
    </div><!-- .nk-block-head -->
    <div class="nk-block">

        {{-- Hidden Input --}}
        <div class="card card-bordered card-preview">
            <div class="card-inner">
                <select class="form-select" aria-label="Default select example" id="premises">
                    @foreach ($checklist as $item)
                        <option value="{{ $item->premises }}"><?= $item->premises ?></option>
                    @endforeach
                </select>

                <div class="tab-content">
                    <div class="row">
                        <div class="col-sm-12 col-md-6">
                            <div id="chartdiv" class="chart"></div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <div id="barChart" class="chart"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- .card-preview -->
    </div>

    <style>
        .chart {
            width: 100%;
            height: 400px;
        }
    </style>
@endsection

@section('script')
    <script>
        //------- PRE INITIALIZE PIE CHART
        var root = am5.Root.new("chartdiv");
        root.setThemes([
            am5themes_Animated.new(root)
        ]);

        //-------- Create chart
        var chartpie = root.container.children.push(am5percent.PieChart.new(root, {
            innerRadius: 50,
            layout: root.verticalLayout
        }));

        var legend;

        //------- END PRE INITIALIZE PIE CHART

        //------- PRE INITIALIZE BAR CHART
        var root2 = am5.Root.new("barChart");
        var chart;

        $(document).ready(function() {
            // getData();
            // showPie(); //--- Default Pie with first index data
            //---- Handle click event for each nav items
            $('.tabku').on('click', function() {

                var id = $(this).attr('id');

                $('.tabku').removeClass('active');
                $(this).addClass('active');

                var premises = $(this).attr('data-premises');
                var baik = $(this).attr('data-baik');
                var kurang = $(this).attr('data-kurang');
                var perlu = $(this).attr('data-perlu-perbaikan');
                var na = $(this).attr('data-not-available');

                $('#premises').val(premises);
                $('#js_baik').val(baik);
                $('#js_kurang').val(kurang);
                $('#js_perlu').val(perlu);
                $('#js_na').val(na);

                //--- dispose chart
                chartpie.series.removeIndex(0).dispose();
                legend.dispose();
                if (chart) {
                    chart.dispose();
                }

                showPie();
            })
            // showBar();
        });

        function showPie(data = null) {
            am5.ready(function() {

                // Create series
                var series = chartpie.series.push(am5percent.PieSeries.new(root, {
                    valueField: "size",
                    categoryField: "sector"
                }));
                var sliceTemplate = series.slices.template;
                sliceTemplate.setAll({
                    draggable: false,
                    templateField: "settings",
                    cornerRadius: 8
                });
                series.slices.template.events.on("click", function(ev) {
                    var category = ev.target.dataItem.dataContext.category;
                    var premises = $('#premises').val();
                    var color = 0x00b503;
                    if (category == 'Kurang Baik') {
                        color = 0xdbd800;
                    } else if (category == 'Perlu Perbaikan') {
                        color = 0xfc0303;
                    } else if (category == 'Not Available') {
                        color = "#c4c4c4";
                    }
                    if (chart) {
                        chart.dispose();
                    }
                    //---- Panggil AJAX JQUERY
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        url: "/load_barchart",
                        data: {
                            kondisi: category,
                            premises: premises
                        },
                        dataType: "json",
                        success: function(response) {
                            const data = response.data;
                            console.log(data);
                            showBar(premises, category, data, color);
                        }
                    });
                });
                series.get("colors").set("colors", [
                    am5.color(0x00b503),
                    am5.color(0xdbd800),
                    am5.color(0xfc0303),
                    am5.color("#c4c4c4"),
                ]);
                // Set data
                // https://www.amcharts.com/docs/v5/charts/percent-charts/pie-chart/#Setting_data
                // series.data.setAll([data]);
                series.data.setAll([{
                        category: "baik",
                        sector: "Baik",
                        size: data[0]['size']
                    },
                    {
                        category: "kurang baik",
                        sector: "Kurang Baik",
                        size: data[1]['size']
                    },
                    {
                        category: "perlu perbaikan",
                        sector: "Perlu Perbaikan",
                        size: data[2]['size']
                    },
                    {
                        category: "not available",
                        sector: "Not Available",
                        size: data[3]['size']
                    }
                ]);
                var sliceTemplate = series.slices.template;
                sliceTemplate.setAll({
                    draggable: false,
                    templateField: "settings",
                    cornerRadius: 4
                });
                // Play initial series animation
                // https://www.amcharts.com/docs/v5/concepts/animations/#Animation_of_series
                series.appear(1000, 100);
                // Add label
                var label = root.tooltipContainer.children.push(am5.Label.new(root, {
                    x: am5.p50,
                    y: am5.p50,
                    centerX: am5.p50,
                    centerY: am5.p50,
                    fill: am5.color(0x000000),
                    fontSize: 50
                }));
                //----
                legend = chartpie.children.push(am5.Legend.new(root, {
                    centerX: am5.percent(50),
                    x: am5.percent(50),
                    marginTop: 15,
                    marginBottom: 15,
                }));
                legend.data.setAll(series.dataItems);
            }); // end am5.ready()
        }
    </script>

    <script>
        function showBar(premises, category, array_result, color) {
            am5.ready(function() {
                // Set themes
                root2.setThemes([
                    am5themes_Animated.new(root2)
                ]);
                // Create chart
                // https://www.amcharts.com/docs/v5/charts/xy-chart/
                chart = root2.container.children.push(am5xy.XYChart.new(root2, {
                    panX: true,
                    panY: true,
                    wheelX: "panX",
                    wheelY: "zoomX",
                    pinchZoomX: true
                }));
                // Add cursor
                // https://www.amcharts.com/docs/v5/charts/xy-chart/cursor/
                var cursor = chart.set("cursor", am5xy.XYCursor.new(root2, {}));
                cursor.lineY.set("visible", false);
                // Create axes
                // https://www.amcharts.com/docs/v5/charts/xy-chart/axes/
                var xRenderer = am5xy.AxisRendererX.new(root2, {
                    minGridDistance: 30
                });
                xRenderer.labels.template.setAll({
                    rotation: -90,
                    centerY: am5.p50,
                    centerX: am5.p100,
                    paddingRight: 15
                });
                var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root2, {
                    maxDeviation: 0.3,
                    categoryField: "label",
                    renderer: xRenderer,
                    tooltip: am5.Tooltip.new(root2, {})
                }));
                var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root2, {
                    maxDeviation: 0.3,
                    renderer: am5xy.AxisRendererY.new(root2, {})
                }));
                // Create series
                // https://www.amcharts.com/docs/v5/charts/xy-chart/series/
                var series = chart.series.push(am5xy.ColumnSeries.new(root2, {
                    name: "Series 1",
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "value",
                    sequencedInterpolation: true,
                    categoryXField: "label",
                    tooltip: am5.Tooltip.new(root2, {
                        labelText: "{valueY}"
                    })
                }));
                series.columns.template.events.on("click", function(ev) {
                    var wilayah = ev.target.dataItem.dataContext.label;
                    location.href = `/cabang/${wilayah}/${category}/${premises}`;
                    // location.href = `/datatable/${premises}/${category}/${wilayah}`;
                });
                series.columns.template.setAll({
                    cornerRadiusTL: 5,
                    cornerRadiusTR: 5
                });
                series.columns.template.adapters.add("fill", function(fill, target) {
                    return chart.get("colors").getIndex(series.columns.indexOf(target));
                });
                series.columns.template.adapters.add("stroke", function(stroke, target) {
                    return chart.get("colors").getIndex(series.columns.indexOf(target));
                });
                chart.get("colors").set("colors", [
                    am5.color(color),
                    am5.color(color),
                    am5.color(color),
                    am5.color(color),
                    am5.color(color),
                    am5.color(color),
                    am5.color(color),
                    am5.color(color),
                    am5.color(color),
                ]);

                var data = array_result;
                xAxis.data.setAll(data);
                series.data.setAll(data);
                series.appear(1000);
                chart.appear(1000, 100);
            }); // end am5.ready()
        }
    </script>

    <script>
        getData()

        $('#premises').change(function(e) {
            e.preventDefault();
            //--- dispose chart
            chartpie.series.removeIndex(0).dispose();
            legend.dispose();
            if (chart) {
                chart.dispose();
            }
            getData();
        });

        function getData() {
            var premises = $("#premises").val()

            $.ajax({
                type: "POST",
                url: "/load_premisesdata",
                data: {
                    premises: premises
                },
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    showPie(response.pieData);
                }
            });
        }
    </script>
@endsection
