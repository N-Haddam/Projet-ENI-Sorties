const inputNom = document.getElementById('nouveauNom');
const selectCodePostal = document.getElementById('nouveauCodePostal');
const selectNoms = document.getElementById('noms')
const linkAjouter = document.getElementById('ajouterVille');
const sectionErreurs = document.getElementById('erreurs')

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

linkAjouter.addEventListener('click', () => {
    // TODO ajout vérif cp len 5
    if (selectCodePostal.value !== '' && inputNom.value !== '') {
        window.location.href = `${urlAjouterVille}?nom=${selectNoms.value}&cp=${selectCodePostal.value}`;
    } else {
        sectionErreurs.innerText = 'Vous devez sélectionner une ville pour l\'ajouter';
        sectionErreurs.classList.add('alert');
        sectionErreurs.classList.add('alert-danger');
    }
})