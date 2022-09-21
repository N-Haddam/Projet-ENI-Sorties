const coordonnees = document.getElementById('coordonnees');
const selectLatitude = document.getElementById('latitude');
const selectLongitude = document.getElementById('longitude');

coordonnees.addEventListener('change', () => {

    let longitude = selectLongitude.value
    let latitude = selectLatitude.value

    var map = new GMaps({
        el: '#map',
        lat: latitude,
        lng: longitude
    });
})



