<template>
    <div>
        <div ref="map" style="width: 100%; height: 100; min-height: 500px;"></div>
    </div>
</template>
<script>
    import axios from "axios";
    export default {
        name: "map",
        data() {
            return {
                platform: {},
                map: {},
                sequenceMarkerGroup: {}
            }
        },
        props: {
            hereApiKey: String,
            hereAppId: String,
            hereAppCode: String,
            latitude: String,
            longitude: String
        },
        created() {
            this.sequenceMarkerGroup = new L.featureGroup();
        },
        async mounted() {
            // const tiles = "https://1.base.maps.api.here.com/maptile/2.1/maptile/newest/normal.day/{z}/{x}/{y}/512/png8?app_id={hereAppId}&app_code={hereAppCode}";
            // const tiles = "https://2.base.maps.ls.hereapi.com/maptile/2.1/trucktile/newest/normal.day/16/35201/21491/256/png8?style=fleet&app_id={hereAppId}&app_code={hereAppCode}";
            // const tiles = "https://2.traffic.maps.ls.hereapi.com/maptile/2.1/trucktile/newest/normal.day/{z}/{x}/{y}/256/png8?apiKey={apiKey}&congestion";
            const tiles = "https://4.base.maps.ls.hereapi.com/maptile/2.1/maptile/newest/{scheme}/{z}/{x}/{y}/{size}/{format}?apiKey={apiKey}&lg={lg}&style={style}&pois{pois}&ppi={ppi}";

            console.log(tiles);

            this.map = new L.Map(this.$refs.map, {
                center: [this.latitude, this.longitude],
                zoom: 10,
                layers: [L.tileLayer(tiles, {
                        hereApiKey: this.hereApiKey,
                        hereAppCode: this.hereAppCode,
                        hereAppId: this.hereAppId,
                        scheme: 'normal.day',
                        size: '512',
                        format: 'png8',
                        lg: 'dut',
                        style: 'default', // fleet
                        pois: false,
                        ppi: 320
                    })]
            });
        },
        methods: {
            dropMarker(position, text) {
                let [lat, lng] = position.split(",");

                let icon = L.divIcon({
                    html: `
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20">
                            <circle cx="25" cy="25" r="25" fill="#000000" />
                            <text x="50%" y="50%" text-anchor="middle" fill="white" font-size="25px" dy=".3em">${text}</text>
                        </svg>
                    `.trim()
                });
                L.marker([lat, lng], { icon: icon }).addTo(this.sequenceMarkerGroup);
                this.sequenceMarkerGroup.addTo(this.map);
                this.map.fitBounds(this.sequenceMarkerGroup.getBounds());
            },
            // async calculateRouteSequence(places) {
            //     let waypoints = {};
            //     for(let i = 0; i < places.length; i++) {
            //         waypoints["destination" + (i + 1)] = `${places[i].latitude},${places[i].longitude}`;
            //     }

            //     axios.defaults.headers.common['Access-Control-Allow-Origin'] = '*';
            //     return axios({
            //         "method": "GET",
            //         "url": "https://wse.api.here.com/2/findsequence.json",
            //         "params": {
            //             "start": `${this.latitude},${this.longitude}`,
            //             ...waypoints,
            //             "end": `${this.latitude},${this.longitude}`,
            //             "mode": "fastest;car;traffic:enabled",
            //             "departure": "now",
            //             "app_id": this.hereAppId,
            //             "app_code": this.hereAppCode
            //         }
            //     }).then(response => {
            //         return response.data.results[0].waypoints
            //     }, error => {
            //         console.error(error);
            //     });
            // },
            drawRoute(result_shape) {

                let shape = result_shape;
                let line = shape.map(point => {
                    let [lat, lng] = point.split(",");
                    return { lat: lat, lng: lng };
                });
                new L.Polyline(line, {snakingSpeed: 2500}).addTo(this.map).snakeIn();

                // let waypoints = {};
                // for(let i = 0; i < sequence.length; i++) {
                //     waypoints["waypoint" + i] = `${sequence[i].lat},${sequence[i].lng}`;
                // }

                // axios({
                //     "method": "GET",
                //     "url": "https://route.api.here.com/routing/7.2/calculateroute.json",
                //     "params": {
                //         "mode": "fastest;car;traffic:enabled",
                //         "representation": "display",
                //         ...waypoints,
                //         "app_id": this.hereAppId,
                //         "app_code": this.hereAppCode
                //     }
                // }).then(result => {
                //     let shape = result.data.response.route[0].shape;
                //     let line = shape.map(point => {
                //         let [lat, lng] = point.split(",");
                //         return { lat: lat, lng: lng };
                //     });
                //     new L.Polyline(line, {snakingSpeed: 500}).addTo(this.map).snakeIn();
                // }, error => {
                //     console.error(error);
                // });
            }
        }
    }
</script>
<style></style>
