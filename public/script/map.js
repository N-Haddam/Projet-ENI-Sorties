const coordonnees = document.getElementById('coordonnees');
let selectLatitude = document.getElementById('latitude');
let selectLongitude = document.getElementById('longitude');
let lat = 46;
let lon = 2 ;

function updateMap() {

}

coordonnees.addEventListener('change', () => {

    lat = selectLatitude.value;
    lon = selectLongitude.value;
    updateMap();
})


let macarte = null;
// Fonction d'initialisation de la carte
function initMap() {
    console.log(lat,lon)
    // Créer l'objet "macarte" et l'insèrer dans l'élément HTML qui a l'ID "map"
    macarte = L.map('map').setView([lat, lon], 11);
    // Leaflet ne récupère pas les cartes (tiles) sur un serveur par défaut. Nous devons lui préciser où nous souhaitons les récupérer. Ici, openstreetmap.fr
    L.tileLayer('https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png', {
        // Il est toujours bien de laisser le lien vers la source des données
        attribution: 'données © <a href="//osm.org/copyright">OpenStreetMap</a>/ODbL - rendu <a href="//openstreetmap.fr">OSM France</a>',
        minZoom: 1,
        maxZoom: 20
    }).addTo(macarte);
}
window.onload = function(){
    // Fonction d'initialisation qui s'exécute lorsque le DOM est chargé
    initMap();
};