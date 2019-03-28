<template>
    <div>
        <div class="card">
            <div class="card-header">Filter</div>
            <div class="card-body">
                <form>
                    <div class="row justify-content-md-center">
                        <div class="col-md-2">
                            <select
                                class="form-control"
                                v-model="municipality"
                                @change="generateBarangays()"
                                autofocus
                            >
                                <option
                                    v-for="(row, index) in municipalities"
                                    :value="row"
                                >{{ row }}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select
                                class="form-control"
                                v-model="barangay"
                                @change="generateData()"
                            >
                                <option v-for="(row, index) in barangays" :value="row">{{ row }}</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card" v-if="data.length">
            <div class="card-header">Result</div>
            <div class="card-body">
                <div class="container">
                    <div class="row">
                        <table class="table table-hover table-responsive">
                            <thead>
                                <tr>
                                    <th scope="col">Project Name</th>
                                    <th scope="col">Year</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Source</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Started</th>
                                    <th scope="col">%</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(row, index) in data"
                                    data-toggle="modal"
                                    data-target="#exampleModal"
                                    @click="showData(index)"
                                >
                                    <td>{{ row.project_name.substring(0, 50) }}</td>
                                    <td>{{ row. year }}</td>
                                    <td>{{ row.infratype }}</td>
                                    <td>{{ row.source_fund }}</td>
                                    <td>{{ formatPrice(row.amount_approp) }}</td>
                                    <td>{{ row.project_started }}</td>
                                    <td>{{ row.accomp_to_date }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Chart</div>
            <div class="card-body">
                <line-chart :chart-data="datacollection" :options="options"></line-chart>
            </div>
        </div>

        <!-- Modal -->
        <div
            class="modal fade"
            id="exampleModal"
            tabindex="-1"
            role="dialog"
            aria-labelledby="exampleModalLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Project Complete Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-6 border-bottom pb-2 pt-2">Year</div>
                            <div class="col-6 border-bottom pb-2 pt-2">{{ selected.year }}</div>
                            <div class="col-6 border-bottom pb-2 pt-2">Project Name</div>
                            <div class="col-6 border-bottom pb-2 pt-2">{{ selected.project_name }}</div>
                            <div class="col-6 border-bottom pb-2 pt-2">Infra Type</div>
                            <div class="col-6 border-bottom pb-2 pt-2">{{ selected.infratype }}</div>
                            <div class="col-6 border-bottom pb-2 pt-2">Infra type 2</div>
                            <div class="col-6 border-bottom pb-2 pt-2">{{ selected.infratype2 }}</div>
                            <div class="col-6 border-bottom pb-2 pt-2">Source of Fund</div>
                            <div class="col-6 border-bottom pb-2 pt-2">{{ selected.source_fund }}</div>
                            <div class="col-6 border-bottom pb-2 pt-2">Amount</div>
                            <div class="col-6 border-bottom pb-2 pt-2">{{ formatPrice(selected.amount_approp) }}</div>
                            <div class="col-6 border-bottom pb-2 pt-2">Project Started</div>
                            <div
                                class="col-6 border-bottom pb-2 pt-2"
                            >{{ selected.project_started }}</div>
                            <div class="col-6 border-bottom pb-2 pt-2">Accomplished</div>
                            <div class="col-6 border-bottom pb-2 pt-2">{{ selected.accomp_to_date }}%</div>
                            <div class="col-6 border-bottom pb-2 pt-2">Date Completed</div>
                            <div class="col-6 border-bottom pb-2 pt-2">{{ selected.datecompleted }}</div>
                            <div class="col-6 border-bottom pb-2 pt-2">Remarks</div>
                            <div class="col-6 border-bottom pb-2 pt-2">{{ selected.remarks }}</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LineChart from "./LineChart.js";

export default {
    components: {
        LineChart
    },
    data() {
        return {
            municipality: "",
            barangay: "",
            municipalities: [],
            barangays: [],
            data: [],
            selected: [],
            options: {
                responsive: true,
                maintainAspectRatio: false,
                tooltips: {
                    enabled: true,
                    mode: "single",
                    callbacks: {
                        label: function(tooltipItem, data) {
                            var self = this
                            var label = data.labels[tooltipItem.index];
                            var datasetLabel =
                                data.datasets[tooltipItem.datasetIndex].data[
                                    tooltipItem.index
                                ];
                            let val = (datasetLabel.toFixed(2) / 1).toFixed(2).replace(",", ".");
                            return label + ": " + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","); 
                        }
                    }
                }
            },
            datacollection: null,
            chart: []
        };
    },
    mounted() {
        axios.get("municipalities").then(response => {
            this.municipalities = response.data;
        });

        this.generateChart();
    },
    methods: {
        generateBarangays() {
            this.barangay = ""
            this.generateData()
            axios
                .post("barangays", { municipality: this.municipality })
                .then(response => {
                    this.barangays = response.data;
                });
        },

        generateData() {
            axios
                .post("data", {
                    municipality: this.municipality,
                    barangay: this.barangay
                })
                .then(response => {
                    this.data = response.data;
                });
        },

        generateChart() {
            axios
                .post("chart", {
                    municipality: this.municipality,
                    barangay: this.barangay
                })
                .then(response => {
                    var self = this;
                    var dynamicColors = function() {
                        var r = Math.floor(Math.random() * 255);
                        var g = Math.floor(Math.random() * 255);
                        var b = Math.floor(Math.random() * 255);
                        return "rgb(" + r + "," + g + "," + b + ")";
                    };
                    var labels = [];
                    var data = [];
                    var colors = [];

                    _.forEach(response.data, function(value, key) {
                        labels.push(value.municipality);
                        data.push(value.total);
                        colors.push(dynamicColors());
                    });

                    self.chart.push({
                        backgroundColor: colors,
                        data: data
                    });

                    this.fillData(self.chart, labels);
                });
        },

        fillData(charts, labels) {
            this.datacollection = {
                labels: labels,
                datasets: charts
            };
        },

        showData(index) {
            var self = this;
            self.selected = self.data[index];
        },

        formatPrice(value) {
            let val = (value / 1).toFixed(2).replace(",", ".");
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
    }
};
</script>
