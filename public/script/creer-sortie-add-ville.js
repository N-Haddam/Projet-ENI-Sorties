const addVille = document.getElementById('addVille');
const addLieu = document.getElementById('addLieu');
const modalVille = document.getElementById('modal-ville')
const closeVille = document.getElementById('close-modal-lieu')

const inputNom = document.getElementById('nouveauNom');
const selectCodePostal = document.getElementById('nouveauCodePostal');
const selectNoms = document.getElementById('noms')
const btnAjouter = document.getElementById('ajouterVille');
const spanMessages = document.getElementById('span-messages');
const sectionMessages = document.getElementById('messages');
// const selectVille = document.getElementById('ville');

addVille.addEventListener('click', () => {
    modalVille.style.display = "block";
})

closeVille.addEventListener('click', () => {
    modalVille.style.display = "none";
})

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
})