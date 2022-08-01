@extends('app')
@section('content')
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
    <div class="row">
        <div class="col-12">
            <div class="card card-bordered card-preview">
                <div class="tab-content">
                    <div class="row">
                        <div class="col-sm-12 col-12">
                            <div id="barChart" class="chart"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
        var root2 = am5.Root.new("barChart");
        var chart;

        $(document).ready(function() {
            var data = @json($data);
            var premises = {!! json_encode($premises) !!};
            var category = {!! json_encode($category) !!};

            showBar(data, premises, category);
        });

        function showBar(array_result, premises, category) {
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
                    var cabang = ev.target.dataItem.dataContext.cabang;
                    var outlet = ev.target.dataItem.dataContext.label;

                    location.href = `/datatable/${premises}/${category}/${outlet}`;
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


                var arrayData = array_result;


                // Set data
                var data = array_result;

                xAxis.data.setAll(data);
                series.data.setAll(data);
                series.appear(1000);
                chart.appear(1000, 100);

            }); // end am5.ready()
        }
    </script>
@endsection
