<template>
    <div>
        <leafletmap
            ref="refmap"
            :here-app-id="hereAppId"
            :here-app-code="hereAppCode"
            :here-api-key="hereApiKey"
            latitude="51.0543"
            longitude="3.7174"
        />
    </div>
</template>
<script>
    import Map from './Leaflet/Map.vue';

    export default {
        name: 'leaflet-map',
        components: {
            'leafletmap': Map,
        },
        props: {
            hereApiKey: {
                type: String,
                default: true,
            },
            hereAppId: {
                type: String,
                default: true,
            },
            hereAppCode: {
                type: String,
                default: true,
            },
            drawRoute: {
                type: Array,
                default: () => [],
            },
            sequence: {
                type: Array,
                default: () => [],
            },
        },
        created() {
            this.sequenceMarkerGroup = new L.featureGroup();
        },
        async mounted() {
            var vm = this;
            let refmap = vm.$refs.refmap;

            for(let i = 0; i < vm.sequence.length - 1; i++) {
                refmap.dropMarker(vm.sequence[i], i.toString());
            }

            refmap.drawRoute(vm.drawRoute);
        }
    }
</script>
<style>
    body {
        margin: 0;
    }
</style>