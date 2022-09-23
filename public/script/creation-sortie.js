
const sectionMessages = document.getElementById('messages');

/* Gestion des modales pour l'ajout d'une ville */
const addVille = document.getElementById('addVille');
const modalVille = document.getElementById('modal-ville');
const closeVille = document.getElementById('close-modal-ville');

addVille.addEventListener('click', () => {
    modalVille.style.display = "block";
})

closeVille.addEventListener('click', () => {
    modalVille.style.display = "none";
})

/* Gestion de l'ajout d'une ville */
const inputNom = document.getElementById('nouveauNom');
const selectCodePostal = document.getElementById('nouveauCodePostal');
const selectNoms = document.getElementById('noms')
const btnAjouterVille = document.getElementById('ajouterVille');
const spanMessagesVille = document.getElementById('span-messages-ville');

inputNom.addEventListener('change', () => {
    let nom = inputNom.value;
    fetch(`https://geo.api.gouv.fr/communes?nom=${nom}`, {
        method: "GET",
        headers: {'Accept': 'application/json'}
    })
        .then(response => response.json())
        .then(response => {
            let options = '<option></option>'
            response.forEach(ville => {
                options += `<option value="${ville.nom}">${ville.nom}</option>`;
            })
            selectNoms.innerHTML = options;
            selectCodePostal.innerHTML = '';
        })
})

selectNoms.addEventListener('change', () => {
    let nom = selectNoms.value;
    fetch(`https://geo.api.gouv.fr/communes?nom=${nom}`, {
        method: "GET",
        headers: {'Accept': 'application/json'}
    })
        .then(response => response.json())
        .then(response => {
            let options;
            response.forEach(element => {
                if (element.nom.toLowerCase() === nom.toLowerCase()) {
                    element.codesPostaux.forEach(codePostal => {
                        options += `<option value="${codePostal}">${codePostal}</option>`
                    })
                }
            })
            selectCodePostal.innerHTML = options;
        })
})

/* Version apiplatform */
btnAjouterVille.addEventListener('click', () => {
    // TODO verif nom non null, cp len 5 et ville n'existe pas déjà
    if (selectNoms.value === '' || selectCodePostal.value.toString().length !== 5) {
        spanMessagesVille.innerText = "Les deux champs sont requis !"
        spanMessagesVille.classList.add('alert');
        spanMessagesVille.classList.add('alert-danger');
    } else {
        fetch(`${urlApiPlatformVille}`, {
            method: "GET",
            headers: {
                'Accept': 'application/json'
            },
        })
            .then(response => response.json())
            .then(response => {
                let villeExist = false;
                response.forEach(ville => {
                    if (ville.nom === selectNoms.value && ville.codePostal.toString() === selectCodePostal.value.toString()) {
                        villeExist = true;
                        // TODO break, mais il faut faire un for et pas un foreach
                    }
                })
                if (villeExist) {
                    inputNom.value = '';
                    selectNoms.innerHTML = '';
                    selectCodePostal.innerHTML = '';
                    sectionMessages.innerText = "La ville existe déjà"
                    sectionMessages.classList.add('alert');
                    sectionMessages.classList.add('alert-danger');
                    modalVille.style.display = "none";
                    // TODO sélectionner la ville dans le select de la création de la sortie
                } else {
                    const ville = {
                        "nom": selectNoms.value,
                        "codePostal": selectCodePostal.value,
                        "lieus": []
                    }
                    fetch(urlApiPlatformVille, {
                        method: "POST",
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(ville)
                    })
                        .then(response => {
                            if (response.status === 201) {
                                response.json()
                                    .then(response => {
                                        sectionMessages.innerText = `La ville a bien été ajoutée : ${response.nom}(${response.codePostal})`;
                                        sectionMessages.classList.add('alert');
                                        sectionMessages.classList.add('alert-success');
                                        selectVille.innerHTML += `<option value="${response.id}" selected>${response.nom} - ${response.codePostal}</option>`;
                                        inputNom.value = '';
                                        selectNoms.innerText = '';
                                        selectCodePostal.innerText = '';
                                        selectLieu.innerHTML = '<option></option>';
                                        modalVille.style.display = "none";
                                    })
                            } else {
                                spanMessagesVille.innerText = 'Une erreur inconnue est survenue';
                                spanMessagesVille.classList.add('alert');
                                spanMessagesVille.classList.add('alert-danger');
                            }
                        })
                }
            })
    }

})

/* Version api perso
btnAjouter.addEventListener('click', () => {
    let nom = selectNoms.value;
    let cp = selectCodePostal.value;
    fetch(`${urlApiAjouterVille}?nom=${nom}&cp=${cp}`, {
        method: "GET",
        headers: {
            'Accept': 'application/json'
        }
    })
        .then(response => response.json())
        .then(response => {
            if (response[0] === '200') {
                sectionMessages.innerText = response[1];
                sectionMessages.classList.add('alert');
                sectionMessages.classList.add('alert-success');
                selectVille.innerHTML += `<option value="${response[2]}" selected>${nom}</option>`;
                inputNom.value = '';
                selectNoms.innerText = '';
                selectCodePostal.innerText = '';
                selectLieu.innerHTML = '<option></option>';
                modalVille.style.display = "none";
            } else if (response[0] === '418') {
                spanMessages.innerText = response[1];
                spanMessages.classList.add('alert');
                spanMessages.classList.add('alert-danger');
            } else {
                spanMessages.innerText = 'Une erreur inconnue est survenue';
                spanMessages.classList.add('alert');
                spanMessages.classList.add('alert-danger');
            }
        })
})*/

/* Gestion des modales pour l'ajout d'un lieu */
const addLieu = document.getElementById('addLieu');
const modalLieu = document.getElementById('modal-lieu');
const closeLieu = document.getElementById('close-modal-lieu');

addLieu.addEventListener('click', () => {
    if (selectVille.value !== '') {
        modalLieu.style.display = "block";
        let idVille = selectVille.value
        let nomVille;
        let cpVille;
        let options;
        fetch(urlApiPlatformVille, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            },
        })
            .then(response => response.json())
            .then(response => {
                response.forEach(ville => {
                    if (ville.id.toString() === idVille.toString()) {
                        options += `<option value="${ville.id}" selected>${ville.nom} - ${ville.codePostal}</option>`;
                        nomVille = ville.nom.toLowerCase();
                        cpVille = ville.codePostal;
                    } else {
                        options += `<option value="${ville.id}">${ville.nom} - ${ville.codePostal}</option>`;
                    }
                })
                selectVilleLieu.innerHTML = options;

                // TODO récupérer lat et long de la ville via api gvt pour centrer la carte
                fetch(`https://api-adresse.data.gouv.fr/search/?q=${nomVille}&postcode=${cpVille}&type=street`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                    },
                })
                    .then(response => response.json())
                    .then(response => {
                        console.log(response)
                        inputLatitude.value = response.features[0].geometry.coordinates[1];
                        inputLongitude.value = response.features[0].geometry.coordinates[0];

                        /* Gestion de la carte pour l'ajout d'un lieu */
                        let map = L.map('map').setView([inputLatitude.value, inputLongitude.value], 13);

                        L.tileLayer('https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png', {
                            // Il est toujours bien de laisser le lien vers la source des données
                            attribution: '© <a href="//osm.org/copyright">OpenStreetMap</a>/ODbL - rendu <a href="//openstreetmap.fr">OSM France</a>',
                            minZoom: 1,
                            maxZoom: 20
                        }).addTo(map);

                        let marker = L.marker([inputLatitude.value, inputLongitude.value], {
                            draggable: true
                        }).addTo(map);
                        marker.bindPopup("<b>Je suis ici !</b>").openPopup();

                        marker.addEventListener('dragend', () => {
                            inputLongitude.value = marker.getLatLng().lng;
                            inputLatitude.value = marker.getLatLng().lat;
                        })

                    })
            })

    } else {
        sectionMessages.innerText = 'Vous devez sélectionner une ville afin d\'ajouter un lieu';
        sectionMessages.classList.add('alert');
        sectionMessages.classList.add('alert-danger');
    }

})

closeLieu.addEventListener('click', () => {
    modalLieu.style.display = "none";
})

/* Gestion de l'ajout d'un lieu */
const btnAjouterLieu = document.getElementById('ajouterLieu');
const inputNomLieu = document.getElementById(('nouveauNomLieu'))
const inputRue = document.getElementById('nouvelleRue')
const inputLatitude = document.getElementById('nouvelleLatitude')
const inputLongitude = document.getElementById('nouvelleLongitude')
const selectVilleLieu = document.getElementById('select-ville-lieu')
const spanMessagesLieu = document.getElementById('span-messages-lieu');

btnAjouterLieu.addEventListener('click', () => {
    // TODO vérifier si la rue correspond bien à la ville et au cp
    // TODO vérifier si les coordonnées correspondent bien à la ville et au cp
    // TODO vérifier si le lieu existe déjà
    let ville = {
        "id": selectVilleLieu.value,
    }
    let lieu = {
        "nom": inputNomLieu.value,
        "rue": inputRue.value,
        "latitude": parseFloat(inputLatitude.value),
        "longitude": parseFloat(inputLongitude.value),
        "ville": ville
    }
    fetch(urlApiPlatformLieu, {
        method: "POST",
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(lieu)
    })
        .then(response => {
            if (response.status === 201) {
                response.json()
                    .then(response => {
                        sectionMessages.innerText = `Le lieu a bien été ajouté : ${response.nom}`;
                        sectionMessages.classList.add('alert');
                        sectionMessages.classList.add('alert-success');
                        selectLieu.innerHTML += `<option value="${response.id}" selected>${response.nom}</option>`;
                        rue.innerText = response.rue;
                        cp.innerText = response.ville.codePostal;
                        latitude.innerText = response.latitude;
                        longitude.innerText = response.longitude;
                        inputNomLieu.value = '';
                        inputRue.value = '';
                        inputLatitude.value = '';
                        inputLongitude.value = '';
                        selectVilleLieu.innerText = '';
                        modalLieu.style.display = "none";
                    })
            } else {
                spanMessagesLieu.innerText = 'Une erreur inconnue est survenue';
                spanMessagesLieu.classList.add('alert');
                spanMessagesLieu.classList.add('alert-danger');
            }
        })
})

const btnRemplir = document.getElementById('remplir');

btnRemplir.addEventListener('click', () => {
    let lati = inputLatitude.value;
    let longi = inputLongitude.value;
    fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lati}&lon=${longi}&format=json`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
        },
    })
        .then(response => response.json())
        .then(response => {
            inputNomLieu.value = response.address.leisure ? response.address.leisure : 'pas de nom renseigné';
            let nomRue = response.address.road;
            let numRue = response.address.house_number;
            inputRue.value = numRue ? numRue + ' ' + nomRue : nomRue;
        })
})