const selectVille = document.getElementById('ville');
const selectLieu = document.getElementById('lieu');
const inputRue = document.getElementById('rue');
const inputCp = document.getElementById('cp');
const inputLatitude = document.getElementById('latitude');
const inputLongitude = document.getElementById('longitude');

let lieux;

selectVille.addEventListener('change', () => {
    let ville = selectVille.value.toString(); // l'id de la ville sélectionnée
    fetch(`${urlApiLieu}/liste/${ville}`, {
        method: "GET",
        headers: {'Accept': 'application/json'}
    })
        .then(response => response.json())
        .then(response => {
            lieux = response;
            let options = "<option></option>";
            response.map(lieu => {
                options += `<option value="${lieu.id}">${lieu.nom}</option>`;
            })
            selectLieu.innerHTML = options;
            inputRue.value = '';
            inputCp.value = '';
            inputLatitude.value = '';
            inputLongitude.value = '';
        })

    // let lieux = ; // liste des lieux de la ville, à faire en requête ajax

    // ajouter au selectLieu en bouclant sur lieux avec <option> par item
        // penser que la sélection est vide au début
})

selectLieu.addEventListener('change', () => {
    let lieu = selectLieu.value; // à vérifier, mais surement l'id du lieu
    lieux.map(l => {
        if (l.id.toString() === lieu.toString()) {
            inputRue.value = l.rue;
            inputCp.value = l.ville.codePostal;
            inputLatitude.value = l.latitude;
            inputLongitude.value = l.longitude;

        }
    })

    // TODO demander à Yohann si je peux passer CP dans lieu plutôt que ville
    // modifier les valeurs des input en fonction du lieu
})