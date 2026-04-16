const mapElement = document.getElementById("map");
const googleMaps = window.google?.maps;

if (mapElement && googleMaps) {
    const center = { lat: 40.69847032728747, lng: -73.9514422416687 };

    const map = new googleMaps.Map(mapElement, {
        center,
        scrollwheel: false,
        styles: [
            {
                featureType: "administrative.country",
                elementType: "geometry",
                stylers: [
                    { visibility: "simplified" },
                    { hue: "#ff0000" },
                ],
            },
        ],
        zoom: 7,
    });

    new googleMaps.Marker({
        map,
        position: center,
    });
}
