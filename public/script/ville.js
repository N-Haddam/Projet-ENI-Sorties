const inputNom = document.getElementById('nouveauNom');
const selectCodePostal = document.getElementById('nouveauCodePostal');
const linkAjouter = document.getElementById('ajouterVille');

inputNom.addEventListener('change', () => {
    let nom = inputNom.value;
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
    if (selectCodePostal.value !== '' && inputNom.value !== '') {
        window.location.href = `${urlAjouterVille}?nom=${selectCodePostal.value}&cp=${inputNom.value}`;
    }
})