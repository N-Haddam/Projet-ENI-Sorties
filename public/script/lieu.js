const selectVille = document.getElementById('ville');
const selectLieu = document.getElementById('lieu');
const rue = document.getElementById('rue');
const cp = document.getElementById('cp');
const latitude = document.getElementById('latitude'); // TODO à retester parce que passer en const
const longitude = document.getElementById('longitude');

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
            rue.innerText = '';
            cp.innerText = '';
            latitude.innerText = '';
            longitude.innerText = '';
        })

    // let lieux = ; // liste des lieux de la ville, à faire en requête ajax

    // ajouter au selectLieu en bouclant sur lieux avec <option> par item
        // penser que la sélection est vide au début
})

selectLieu.addEventListener('change', () => {
    let lieu = selectLieu.value; // à vérifier, mais surement l'id du lieu
    console.log('coucou')
    lieux.map(l => {
        if (l.id.toString() === lieu.toString()) {
            rue.innerText = l.rue;
            cp.innerText = l.ville.codePostal;
            latitude.innerText = l.latitude;
            longitude.innerText = l.longitude;

        }
    })
})