const coordonnees = document.getElementById('coordonnees');
let selectLatitude = document.getElementById('latitude');
let selectLongitude = document.getElementById('longitude');
let ville = document.getElementById('ville');

let mymap, marker
let latitude = 48.852969;
let longitude = 2.349903;

console.log(ville);

// On attend que le DOM soit chargé
window.onload = () => { // Nous initialisons la carte et nous la centrons sur Paris
mymap = L.map('map').setView([48.852969, 2.349903], 11)
L.tileLayer('https://maps.wikimedia.org/osm-intl/{z}/{x}/{y}.png', {
    attribution: 'Carte fournie par Wikimedia',
    minZoom: 1,
    maxZoom: 20
    }).addTo(mymap)
    mymap.on('click', mapClickListen)

}


function mapClickListen(e) {
    let pos = e.latlng

    //Ajout marqueur
    addMarker(pos)

    //afficher coord dans le formulaire
    document.querySelector("#latitude").value = pos.lat
    document.querySelector("#longitude").value = pos.lng
}


function addMarker(pos) {

    if(marker != undefined){
        mymap.removeLayer(marker)
    }
    marker = L.marker(pos, {
        draggable: true //marker devient deplaçable
    })

    //ecouter glisser déposer sur le marker
    marker.on("dragend", function (e){
        pos = e.target.getLatLng()
        document.querySelector("#latitude").value = pos.lat
        document.querySelector("#longitude").value = pos.lng

    })

    marker.addTo(mymap)
}

// let lat = 46;
// let lon = 2 ;
// let macarte = null;
// // Fonction d'initialisation de la carte
// function initMap() {
//     console.log(lat,lon)
//     // Créer l'objet "macarte" et l'insèrer dans l'élément HTML qui a l'ID "map"
//     macarte = L.map('map').setView([lat, lon], 11);
//     // Leaflet ne récupère pas les cartes (tiles) sur un serveur par défaut. Nous devons lui préciser où nous souhaitons les récupérer. Ici, openstreetmap.fr
//     L.tileLayer('https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png', {
//         // Il est toujours bien de laisser le lien vers la source des données
//         attribution: 'données © <a href="//osm.org/copyright">OpenStreetMap</a>/ODbL - rendu <a href="//openstreetmap.fr">OSM France</a>',
//         minZoom: 1,
//         maxZoom: 20
//     }).addTo(macarte);
//
//     var marker = L.marker([lat,lon]).addTo(macarte);
//     marker.bindPopup("<p>Nouveau lieu</p>");
// }
// window.onload = function(){
//     // Fonction d'initialisation qui s'exécute lorsque le DOM est chargé
//     initMap();
// };